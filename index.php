<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Tony Thomas">
    <title>AWS Inventory Collector</title>
    <link rel="shortcut icon" href="https://tonythomas.in/wp-content/uploads/2024/02/cropped-tony-thomas-favicon-180x180.webp" type="image/x-icon">
    <!-- Bootstrap 4 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" media="print" onload="this.media='all'; this.onload = null">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">
            <img src="https://tonythomas.in/wp-content/uploads/2024/02/tony-thomas-logo-invert.webp" alt="" width="250" height="65" class="d-inline-block align-text-top">
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
              <a class="nav-link active" aria-current="page" href="#">Home</a>
              <a class="nav-link" href="https://awspolicygen.s3.amazonaws.com/policygen.html" target="_blank">AWS Policy Generator</a>
              <a class="nav-link" href="https://calculator.aws/" target="_blank">AWS Pricing Calculator</a>
            </div>
          </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 mx-auto">
                <div class="my-5">
                    <div class="card border-warning shadow">
                        <div class="card-body">
                            <form id="inventoryCollectorFormAWS" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="accountName">Account Name</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="accountName" name="accountName" placeholder="Account Name"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="accountId">Account ID</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <input type="text" class="form-control" id="accountId" name="accountId" placeholder="Account ID"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="accessKeyId">Access Key ID</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                            <input type="text" class="form-control" id="accessKeyId" name="accessKeyId" placeholder="Access Key ID"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="secretAccessKey">Secret Access Key</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                            <input type="password" class="form-control" id="secretAccessKey" name="secretAccessKey" placeholder="Secret Access Key"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="service">Service</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fab fa-aws"></i></span>
                                            <select class="form-select select2-service" id="service" name="service">
                                                <option value=""></option>
                                                <option value="ec2">EC2</option>
                                                <option value="rds">RDS</option>
                                                <option value="ami">AMI</option>
                                                <option value="volume">Volume</option>
                                                <option value="snapshot">Snapshot</option>
                                                <option value="s3">S3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="region">Region</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-globe-asia"></i></span>
                                            <select class="form-select select2-region" id="region" name="region">
                                                <option value=""></option>
                                                <option value="us-east-1">US East (N. Virginia)</option>
                                                <option value="us-east-2">US East (Ohio)</option>
                                                <option value="us-west-1">US West (N. California)</option>
                                                <option value="us-west-2">US West (Oregon)</option>
                                                <option value="ap-south-1">Asia Pacific (Mumbai)</option>
                                                <option value="ap-northeast-1">Asia Pacific (Tokyo)</option>
                                                <option value="ap-northeast-2">Asia Pacific (Seoul)</option>
                                                <option value="ap-southeast-1">Asia Pacific (Singapore)</option>
                                                <option value="ap-east-1">Asia Pacific (Hong Kong)</option>
                                                <option value="eu-west-1">Europe (Ireland)</option>
                                                <option value="eu-west-2">Europe (London)</option>
                                                <option value="eu-central-1">Europe (Frankfurt)</option>
                                                <option value="eu-north-1">Europe (Stockholm)</option>
                                                <option value="eu-south-1">Europe (Milan)</option>
                                                <option value="sa-east-1">South America (São Paulo)</option>
                                                <option value="me-south-1">Middle East (Bahrain)</option>
                                                <option value="af-south-1">Africa (Cape Town)</option>
                                                <option value="ap-northeast-3">Asia Pacific (Osaka)</option>
                                                <option value="ap-southeast-3">Asia Pacific (Jakarta)</option>
                                                <option value="us-west-3">US West (Las Vegas)</option>
                                                <option value="eu-south-2">Europe (Spain)</option>
                                                <option value="us-gov-west-1">US West (GovCloud)</option>
                                                <option value="us-gov-east-1">US East (GovCloud)</option>
                                                <option value="eu-central-2">Europe (Zurich)</option>
                                                <option value="ap-south-2">Asia Pacific (Hyderabad)</option>
                                                <option value="ap-south-3">Asia Pacific (Chennai)</option>
                                                <option value="ap-southeast-4">Asia Pacific (Bangkok)</option>
                                                <option value="cn-north-1">Asia Pacific (Beijing)</option>
                                                <option value="cn-northwest-1">Asia Pacific (Ningxia)</option>
                                                <option value="eu-west-3">Europe (Paris)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning float-end" id="fetchInventory" name="fetchInventory">
                                    <i class="fas fa-cloud-download-alt"></i> <b>Fetch Inventory</b>
                                </button>
                            </form>
                        </div>
                        <!-- Progress Bar -->
                        <div class="progress mt-3" id="progressBarContainer" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                id="progressBar" role="progressbar" 
                                style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery 3.7.1 -->    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="../../assets/js/aws-inventory-collector.js"></script>
</body>
</html>
