# AWS Inventory Collector

## Overview
The AWS Inventory Collector is a PHP-based tool to collect and export inventory data from various AWS services (EC2, RDS, AMI, Volumes, Snapshots, and S3) into CSV files. It uses the AWS SDK for PHP.

## Features
- Supports AWS services: EC2, RDS, AMI, Volumes, Snapshots, and S3.
- Exports inventory data as CSV.
- AWS SDK integration for seamless API interaction.

## Prerequisites
- PHP 7.2 or higher
- AWS SDK for PHP
- AWS credentials (Access Key, Secret Key)

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/thomas-tony/aws-inventory-collector.git
   ```
2. **Run the application**:
   Place the project in a PHP-supported server (like Apache or Nginx).
   
## Usage
1. Open the application in your browser.
2. Enter your AWS credentials (Access Key, Secret Key) and choose the AWS service to collect data.
3. Export the data to a CSV file.
