<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Tony Thomas">
    <title>AWS Inventory Collector</title>
    <link rel="shortcut icon" href="https://tonythomas.in/wp-content/uploads/2024/02/cropped-tony-thomas-favicon-180x180.webp" type="image/x-icon">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/alt/adminlte.light.min.css" integrity="sha512-sH43x9hDH6VYZCimbGd58vYrO4uMdmPn3m8QUgxNYi4MNmj4sbt+fN1jG+TnVA2Q0SA6tvEo6W6P1Z0FA+6AXA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" media="print" onload="this.media='all'; this.onload = null">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/7.2.3/css/flag-icons.min.css" integrity="sha512-bZBu2H0+FGFz/stDN/L0k8J0G8qVsAL0ht1qg5kTwtAheiXwiRKyCq1frwfbSFSJN3jooR5kauE0YjtPzhZtJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Styles -->
     <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navbar -->            
    <nav id="main-navbar" class="main-header navbar navbar-expand navbar-dark">
        <ul class="navbar-nav">
            <li class="nav-item">
                <img src="https://tonythomas.in/wp-content/uploads/2024/02/tony-thomas-logo-invert.webp" alt="" width="165" height="40" class="d-inline-block align-text-top">
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="https://awspolicygen.s3.amazonaws.com/policygen.html" class="nav-link" target="_blank" title="AWS Policy Generator"><b><i class="fas fa-code"></i> AWS Policy Generator</b></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="https://calculator.aws/#/addService" class="nav-link" target="_blank" title="AWS Pricing Calculator"><b><i class="fas fa-calculator"></i> AWS Pricing Calculator</b></a>
            </li>
        </ul>
    </nav>
    <!-- ./Navbar -->
    <!-- Wrapper -->
	<div class="wrapper">
		<div class="content-wrapper">
			<section class="content">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-8 mx-auto">
						<div class="my-4">
							<div class="card card-warning card-outline shadow">
								<div class="card-body">
									<form id="inventoryCollectorFormAWS" method="post">
										<div class="row">
											<div class="col-md-6">
												<label for="Account Name">Account Name</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-user"></i></span>
													</div>
													<input type="text" class="form-control" id="accountName" name="accountName" placeholder="Account Name" autocomplete="off"/>
												</div>
											</div>
											<div class="col-md-6">
												<label for="Account ID">Account ID</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-id-card"></i></span>
													</div>
													<input type="text" class="form-control" id="accountId" name="accountId" placeholder="Account ID" autocomplete="off"/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<label for="Access Key ID">Access Key ID</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
													</div>
													<input type="text" class="form-control" id="accessKeyId"  name="accessKeyId" placeholder="Access Key ID" autocomplete="off"/>
												</div>
											</div>
											<div class="col-md-6">
												<label for="Secret Access Key">Secret Access Key</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-key"></i></span>
													</div>
													<input type="password" class="form-control" id="secretAccessKey" name="secretAccessKey" placeholder="Secret Access Key" autocomplete="off"/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<label for="Session Token">Session Token (If Available)</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
													</div>
													<textarea class="form-control" id="sessionToken" name="sessionToken" rows="1" placeholder="Paste your session token here"></textarea>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<label for="Service">Service</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fab fa-aws"></i></span>
													</div>
													<select class="form-control select2-service" id="service" name="service">
														<option value=""></option>
														<option value="ec2" data-icon="fas fa-microchip">EC2s</option>
														<option value="ami" data-icon="fas fa-compact-disc">AMIs</option>
														<option value="volume" data-icon="fas fa-hdd">Volumes</option>
														<option value="ebs_snapshot" data-icon="fas fa-archive">EBS Snapshots</option>
														<option value="rds" data-icon="fas fa-database">RDS</option>
														<option value="rds_snapshot" data-icon="fas fa-archive">RDS Snapshots</option>
														<option value="s3" data-icon="fab fa-bitbucket">S3</option>
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<label for="Select Region">Region</label>
												<div class="form-group input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-globe-asia"></i></span>
													</div>
													<select class="form-control select2-region" id="region" name="region">
														<option value=""></option>
														<option value="ap-south-1" data-flag="fi fi-in">Asia Pacific (Mumbai)</option>
														<option value="ap-south-2" data-flag="fi fi-in">Asia Pacific (Hyderabad)</option>
														<option value="us-east-1" data-flag="fi fi-us">US East (N. Virginia)</option>
														<option value="af-south-1" data-flag="fi fi-za">Africa (Cape Town)</option>
														<option value="ap-east-1" data-flag="fi fi-hk">Asia Pacific (Hong Kong)</option>
														<option value="ap-northeast-1" data-flag="fi fi-jp">Asia Pacific (Tokyo)</option>
														<option value="ap-northeast-2" data-flag="fi fi-sk">Asia Pacific (Seoul)</option>
														<option value="ap-northeast-3" data-flag="fi fi-jp">Asia Pacific (Osaka)</option>
														<option value="ap-southeast-1" data-flag="fi fi-sg">Asia Pacific (Singapore)</option>
														<option value="ap-southeast-2" data-flag="fi fi-au">Asia Pacific (Sydney)</option>
														<option value="ap-southeast-3" data-flag="fi fi-id">Asia Pacific (Jakarta)</option>
														<option value="ap-southeast-4" data-flag="fi fi-au">Asia Pacific (Melbourne)</option>
														<option value="ap-southeast-5" data-flag="fi fi-my">Asia Pacific (Malaysia)</option>
    													<option value="ap-southeast-7" data-flag="fi fi-th">Asia Pacific (Thailand)</option>
														<option value="ca-central-1" data-flag="fi fi-ca">Canada (Montreal)</option>
														<option value="ca-west-1" data-flag="fi fi-ca">Canada West (Calgary)</option>
														<option value="cn-north-1" data-flag="fi fi-cn">China (Beijing)</option>
														<option value="cn-northwest-1" data-flag="fi fi-cn">China (Ningxia)</option>
														<option value="eu-central-1" data-flag="fi fi-de">Europe (Frankfurt)</option>
														<option value="eu-central-2" data-flag="fi fi-ch">Europe (Zurich)</option>
														<option value="eu-north-1" data-flag="fi fi-se">Europe (Stockholm)</option>
														<option value="eu-south-1" data-flag="fi fi-it">Europe (Milan)</option>
														<option value="eu-south-2" data-flag="fi fi-es">Europe (Spain)</option>
														<option value="eu-west-1" data-flag="fi fi-ie">Europe (Ireland)</option>
														<option value="eu-west-2" data-flag="fi fi-gb">Europe (London)</option>
														<option value="eu-west-3" data-flag="fi fi-fr">Europe (Paris)</option>
														<option value="il-central-1" data-flag="fi fi-il">Israel (Tel Aviv)</option>
														<option value="mx-central-1" data-flag="fi fi-mx">Mexico (Central)</option>
														<option value="me-central-1" data-flag="fi fi-ae">Middle East (UAE)</option>
														<option value="me-south-1" data-flag="fi fi-bh">Middle East (Bahrain)</option>
														<option value="sa-east-1" data-flag="fi fi-br">South America (Sao Paulo)</option>
														<option value="us-east-2" data-flag="fi fi-us">US East (Ohio)</option>
														<option value="us-gov-east-1" data-flag="fi fi-us">AWS GovCloud (US-East)</option>
														<option value="us-gov-west-1" data-flag="fi fi-us">AWS GovCloud (US-West)</option>
														<option value="us-west-1" data-flag="fi fi-us">US West (N. California)</option>
														<option value="us-west-2" data-flag="fi fi-us">US West (Oregon)</option>
													</select>

												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-warning float-right" id="fetchInventory" name="fetchInventory"><i class="fas fa-cloud-download-alt"></i> <b>Fetch Inventory</b></button>
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
			</section>
		</div>
	</div>	
    <!-- Footer -->            
    <footer class="main-footer">
        <div class="row text-center">
            <div class="col-md-4 box">
                <span>Copyright &copy; 2024 - <?php echo date('Y'); ?></span>
            </div>
            <div class="col-md-4 footer-credit">
                <a href="javascript:void(0)" target="_blank"> <b>AWS Inventory Collector</b></a>
            </div>
            <div class="col-md-4">
                Made with <i class="fas fa-heart text-danger"></i> by <a href="https://www.linkedin.com/in/tony-thomas-116b4512b" target="_blank"><b>Tony Thomas</b></a> 
            </div>
        </div>
        <a id="back-to-top" href="#" class="btn  btn-flat btn-primary back-to-top" role="button"><i class="fas fa-chevron-up"></i></a>
    </footer>
    <!-- ./Footer -->
    <!-- Jquery 3.7.1 -->    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- AdminLTE 3 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js" integrity="sha512-KBeR1NhClUySj9xBB0+KRqYLPkM6VvXiiWaSz/8LCQNdRpUm38SWUrj0ccNDNSkwCD9qPA4KobLliG26yPppJA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="../../assets/js/aws-inventory-collector.js"></script>
</body>
</html>
