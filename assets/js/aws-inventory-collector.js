
/* AWS Inventory Collector Form Validation */
$("#inventoryCollectorFormAWS").validate({
    ignore: ".ignore",
    rules: {
        accountName: {
            required: true,
        },
        accountId: {
            required: true,
        },
        accessKeyId: {
            required: true,
        },
        secretAccessKey: {
            required: true,
        },
        service: {
            required: true,
        },
        region: {
            required: true,
        },
        hiddenHCaptcha: {
            required: function() {
                if($('[name=h-captcha-response]').val() == '') {
                    return true;
                } else {
                    return false;
                }
            },
        },
    },
    messages: {
        accountName: {
            required: "Please enter AWS Account Name.",
        },
        accountId: {
            required: "Please enter AWS Account Id.",
        },
        accessKeyId: {
            required: "Please enter IAM Access Key Id.",
        },
        secretAccessKey: {
            required: "Please enter Secret Access Key",
        },
        service: {
            required: "Please select an AWS Service.",
        },
        region: {
            required: "Please select an AWS Geographic Region.",
        },
        hiddenHCaptcha: {
            required: "Please complete the captcha challenge.",
        }
    },
    submitHandler: function (form, event) {
        $('#fetchInventory').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <b>Fetching..</b>');
        event.preventDefault();
        // Form submission via AJAX
        // Reset progress bar and show it
        $('#progressBarContainer').show();
        updateProgressBar(0);
    
        var formData = {
            accountName: $('#accountName').val(),
            accountId: $('#accountId').val(),
            accessKeyId: $('#accessKeyId').val(),
            secretAccessKey: $('#secretAccessKey').val(),
            service: $('#service').val(),
            region: $('#region').val()
        };

        $.ajax({
            type: 'POST',
            url: '../../includes/ajax/aws-inventory-collector.php',
            data: formData,
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: function() {
                updateProgressBar(20);  
            },
            success: function (data, status, xhr) {
                updateProgressBar(70);  
                
                var blob = new Blob([data], { type: 'text/csv' });
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                var filename = xhr.getResponseHeader('Content-Disposition').split('filename=')[1].replace(/"/g, '');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
    
                setTimeout(function() {
                    updateProgressBar(100);
                    $('#fetchInventory').prop('disabled', false).html('<i class="fas fa-cloud-download-alt"></i> <b>Fetch Inventory</b>');
                }, 500);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching inventory:', error);
                $('#progressBarContainer').hide();
                $('#fetchInventory').prop('disabled', false).html('<i class="fas fa-cloud-download-alt"></i> <b>Fetch Inventory</b>');
            },
            complete: function() {
                // Hide progress bar after the process is complete
                setTimeout(function() {
                    $('#progressBarContainer').hide();
                    updateProgressBar(0);
                }, 1000);
            }
        });

        function updateProgressBar(percentage) {
            $('#progressBar').css('width', percentage + '%').attr('aria-valuenow', percentage).text(percentage + '%');
        }
    },

    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid');
        $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
    }
});


    /* Form Custom Select Initialization*/
$('.select2-service').select2({
    theme: 'bootstrap4',
    placeholder: 'Select AWS Service',
}).on('select2:opening', function(e) {
    $(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', 'Search AWS Service')
});
$('.select2-region').select2({
    theme: 'bootstrap4',
    placeholder: 'Select AWS Region',
}).on('select2:opening', function(e) {
    $(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', 'Search AWS Region')
});