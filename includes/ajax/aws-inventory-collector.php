<?php
require $_SERVER['DOCUMENT_ROOT'] . '/libraries/aws-sdk-php/aws-autoloader.php';

use Aws\Ec2\Ec2Client;
use Aws\Rds\RdsClient;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Get form inputs
$accountName = $_POST['accountName'];
$accountId = $_POST['accountId'];
$accessKeyId = $_POST['accessKeyId'];
$secretAccessKey = $_POST['secretAccessKey'];
$service = $_POST['service'];
$region = $_POST['region'];

// Initialize AWS SDK client
$sdkConfig = [
    'region' => $region == 'all' ? 'us-east-1' : $region,
    'version' => 'latest',
    'credentials' => [
        'key' => $accessKeyId,
        'secret' => $secretAccessKey,
    ],
];

// Fetch inventory based on the service selected
$inventoryData = [];

try {
    switch ($service) {
        case 'ec2':
            $ec2Client = new Ec2Client($sdkConfig);
            $result = $ec2Client->describeInstances();
            // Extract relevant data from the EC2 instances
            foreach ($result['Reservations'] as $reservation) {
                foreach ($reservation['Instances'] as $instance) {
                    // Get Instance Name (from tags)
                    $instanceName = 'N/A';
                    if (!empty($instance['Tags'])) {
                        foreach ($instance['Tags'] as $tag) {
                            if ($tag['Key'] === 'Name') {
                                $instanceName = $tag['Value'];
                                break;
                            }
                        }
                    }

                    // Get Security Group Names
                    $securityGroups = [];
                    foreach ($instance['SecurityGroups'] as $sg) {
                        $securityGroups[] = $sg['GroupName'];
                    }
                    $securityGroupName = implode(', ', $securityGroups);

                    // Get IPv6 addresses
                    $ipv6Addresses = [];
                    if (!empty($instance['NetworkInterfaces'])) {
                        foreach ($instance['NetworkInterfaces'] as $interface) {
                            foreach ($interface['Ipv6Addresses'] as $ipv6) {
                                $ipv6Addresses[] = $ipv6['Ipv6Address'];
                            }
                        }
                    }
                    $ipv6Ips = implode(', ', $ipv6Addresses);

                    // Add instance data to inventory
                    $inventoryData[] = [
                        'AccountId' => $accountId,
                        'AccountName' => $accountName,
                        'Instance Name' => $instanceName,
                        'Instance ID' => $instance['InstanceId'],
                        'Instance state' => $instance['State']['Name'],
                        'Instance type' => $instance['InstanceType'],
                        'Status check' => $instance['State']['Name'],  // Placeholder, can update with detailed status check
                        'Alarm status' => 'N/A',  // Placeholder for CloudWatch Alarm status
                        'Availability Zone' => $instance['Placement']['AvailabilityZone'],
                        'Public IPv4 DNS' => $instance['PublicDnsName'] ?? 'N/A',
                        'Public IPv4 address' => $instance['PublicIpAddress'] ?? 'N/A',
                        'Elastic IP' => !empty($instance['NetworkInterfaces'][0]['Association']['PublicIp']) ? $instance['NetworkInterfaces'][0]['Association']['PublicIp'] : 'N/A',
                        'IPv6 IPs' => $ipv6Ips ?: 'N/A',
                        'Private IP address' => $instance['PrivateIpAddress'],
                        'Monitoring' => $instance['Monitoring']['State'],
                        'Security group name' => $securityGroupName,
                        'Key name' => $instance['KeyName'] ?? 'N/A',
                        'Launch time' => $instance['LaunchTime']->format('Y-m-d H:i:s'),
                        'Platform details' => $instance['PlatformDetails'] ?? 'N/A',
                    ];
                }
            }
            break;
        case 'ami':
            $ec2Client = new Ec2Client($sdkConfig);
            $result = $ec2Client->describeImages([
                'Owners' => [$accountId] // Filter images owned by this account
            ]);

            // Extract relevant AMI data
            foreach ($result['Images'] as $image) {
                $blockDeviceMappings = [];
                foreach ($image['BlockDeviceMappings'] as $bdm) {
                    $blockDeviceMappings[] = $bdm['DeviceName'];
                }
                $blockDevices = implode(', ', $blockDeviceMappings);

                $inventoryData[] = [
                    'AMI name' => $image['Name'] ?? 'N/A',
                    'AMI ID' => $image['ImageId'] ?? 'N/A',
                    'Source' => $image['ImageLocation'] ?? 'N/A',
                    'Owner' => $image['OwnerId'] ?? 'N/A',
                    'Visibility' => $image['Public'] ? 'Public' : 'Private',
                    'Status' => $image['State'] ?? 'N/A',
                    'Creation date' => $image['CreationDate'] ?? 'N/A',
                    'Platform' => $image['Platform'] ?? 'N/A',
                    'Root device type' => $image['RootDeviceType'] ?? 'N/A',
                    'Block devices' => $blockDevices ?: 'N/A',
                    'Virtualization' => $image['VirtualizationType'] ?? 'N/A',
                    'Deprecation time' => $image['DeprecationTime'] ?? 'N/A',
                    'Last launched time' => $image['LastLaunchedTime'] ?? 'N/A',
                    'Deregistration protection' => $image['ImageOwnerAlias'] ?? 'N/A',
                ];
            }
            break;
        
        case 'volume':
            $ec2Client = new Ec2Client($sdkConfig);
            $result = $ec2Client->describeVolumes();

            // Extract relevant volume data
            foreach ($result['Volumes'] as $volume) {
                $attachedResources = [];
                foreach ($volume['Attachments'] as $attachment) {
                    $attachedResources[] = $attachment['InstanceId'] ?? 'N/A';
                }
                $attachedResourcesStr = implode(', ', $attachedResources);

                $inventoryData[] = [
                    'Volume Name' => $volume['Tags'][0]['Value'] ?? 'N/A', // Assuming first tag is the name
                    'Volume ID' => $volume['VolumeId'] ?? 'N/A',
                    'Type' => $volume['VolumeType'] ?? 'N/A',
                    'Size (GiB)' => $volume['Size'] ?? 'N/A',
                    'IOPS' => $volume['Iops'] ?? 'N/A',
                    'Throughput (MB/s)' => $volume['Throughput'] ?? 'N/A',
                    'Snapshot ID' => $volume['SnapshotId'] ?? 'N/A',
                    'Created' => $volume['CreateTime'] ?? 'N/A',
                    'Availability Zone' => $volume['AvailabilityZone'] ?? 'N/A',
                    'Volume State' => $volume['State'] ?? 'N/A',
                    'Alarm Status' => 'N/A', // AWS doesn't provide direct alarm info for volumes, you may need CloudWatch for this
                    'Attached Resources' => $attachedResourcesStr,
                    'Volume Status' => $volume['State'] ?? 'N/A', // Volume state also shows volume status
                    'Encryption' => $volume['Encrypted'] ? 'Encrypted' : 'Not Encrypted',
                    'KMS Key ID' => $volume['KmsKeyId'] ?? 'N/A',
                    'KMS Key Alias' => 'N/A', // This info may require a separate call to KMS to retrieve aliases
                    'Fast Snapshot Restored' => $volume['FastRestored'] ? 'Yes' : 'No',
                    'Multi-Attach Enabled' => $volume['MultiAttachEnabled'] ? 'Yes' : 'No',
                ];
            }
            break;
        
        case 'snapshot': // New case for EBS Snapshots
            $ec2Client = new Ec2Client($sdkConfig);
            $result = $ec2Client->describeSnapshots([
                'OwnerIds' => [$accountId] // Only retrieve snapshots owned by this account
            ]);

            // Extract relevant snapshot data
            foreach ($result['Snapshots'] as $snapshot) {
                $inventoryData[] = [
                    'Name' => $snapshot['Tags'][0]['Value'] ?? 'N/A', // Assuming first tag is the snapshot name
                    'Snapshot ID' => $snapshot['SnapshotId'] ?? 'N/A',
                    'Volume Size (GiB)' => $snapshot['VolumeSize'] ?? 'N/A',
                    'Description' => $snapshot['Description'] ?? 'N/A',
                    'Storage Tier' => $snapshot['StorageTier'] ?? 'N/A', // Storage tier info
                    'Snapshot Status' => $snapshot['State'] ?? 'N/A',
                    'Started' => $snapshot['StartTime'] ?? 'N/A',
                    'Progress' => $snapshot['Progress'] ?? 'N/A',
                    'Encryption' => $snapshot['Encrypted'] ? 'Encrypted' : 'Not Encrypted',
                    'KMS Key ID' => $snapshot['KmsKeyId'] ?? 'N/A',
                    'KMS Key Alias' => 'N/A', // Requires separate KMS call to fetch alias
                    'Outposts ARN' => $snapshot['OutpostArn'] ?? 'N/A',
                ];
            }
            break;
        
        case 'rds':
            $rdsClient = new RdsClient($sdkConfig);
            $result = $rdsClient->describeDBInstances();
            foreach ($result['DBInstances'] as $dbInstance) {
                $inventoryData[] = [
                    'AccountName' => $accountName,
                    'AccountId' => $accountId,
                    'DBInstanceIdentifier' => $dbInstance['DBInstanceIdentifier'],
                    'InstanceCreateTime' => isset($dbInstance['InstanceCreateTime']) ? $dbInstance['InstanceCreateTime']->format(DateTime::ISO8601) : 'N/A', 
                    'Engine' => $dbInstance['Engine'],
                    'Status' => $dbInstance['DBInstanceStatus'],
                    'AllocatedStorage' => $dbInstance['AllocatedStorage'],
                    'DBInstanceClass' => $dbInstance['DBInstanceClass'],
                    'MasterUsername' => $dbInstance['MasterUsername'],
                    'EngineVersion' => $dbInstance['EngineVersion'],
                    'LicenseModel' => $dbInstance['LicenseModel'],
                    'DBInstanceArn' => $dbInstance['DBInstanceArn'],
                    'IAMDatabaseAuthenticationEnabled' => $dbInstance['IAMDatabaseAuthenticationEnabled'] ? 'Enabled' : 'Disabled',
                    'Region' => $region,
                ];
            }
            break;

        case 's3': // New case for S3
            $s3Client = new S3Client($sdkConfig);

            // List all buckets
            $result = $s3Client->listBuckets();

            // Iterate over each bucket to fetch more details
            foreach ($result['Buckets'] as $bucket) {
                $bucketName = $bucket['Name'];
                $bucketCreationDate = $bucket['CreationDate'];

                // Fetch the bucket location (region)
                $locationResult = $s3Client->getBucketLocation(['Bucket' => $bucketName]);
                $bucketRegion = $locationResult['LocationConstraint'] ?? 'us-east-1'; // Default to us-east-1 if not specified

                // Check IAM Access Analyzer for public access policy status
                try {
                    $policyStatusResult = $s3Client->getBucketPolicyStatus(['Bucket' => $bucketName]);
                    $accessAnalyzerStatus = $policyStatusResult['PolicyStatus']['IsPublic'] ? 'Public' : 'Private';
                } catch (AwsException $e) {
                    $accessAnalyzerStatus = 'N/A'; // If policy check fails, default to N/A
                }

                // Add bucket data to inventory
                $inventoryData[] = [
                    'Name' => $bucketName,
                    'AWS Region' => $bucketRegion,
                    'IAM Access Analyzer' => $accessAnalyzerStatus,
                    'Creation date' => $bucketCreationDate->format('Y-m-d H:i:s'),
                ];
            }
            break;

        default:
            throw new Exception("Unsupported service selected.");
    }

    // Create the filename for the CSV
    $date = date('Y-m-d');
    $filename = strtolower(str_replace(' ', '-', $accountName)) . '-' . strtolower($service) . '-inventory-' . $date . '.csv';

    // Set headers to force download of the CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream to the browser instead of creating a file on the server
    $output = fopen('php://output', 'w');

    // Write the header row if there's data
    if (!empty($inventoryData)) {
        fputcsv($output, array_keys($inventoryData[0]));
    }

    // Write each row of instance data
    foreach ($inventoryData as $data) {
        fputcsv($output, $data);
    }

    // Close the output stream
    fclose($output);
    exit(); // End the script after outputting the CSV

} catch (AwsException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
