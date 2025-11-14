<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();

// Debug: Check what parameters are passed
if(isset($_GET['debug'])) {
	echo '<pre style="background: #f4f4f4; padding: 15px; margin: 15px;">';
	echo "GET Parameters:\n";
	print_r($_GET);
	echo "\nPOST Parameters:\n";
	print_r($_POST);
	echo '</pre>';
}

// get page data - always initialize if section is provided
if(isset($_GET['section'])) {
	p_init();
	// Debug: Check if initialization worked
	if(isset($_GET['debug'])) {
		echo '<pre style="background: #e7f3ff; padding: 15px; margin: 15px;">';
		echo "After p_init():\n";
		echo "Page data exists: " . (isset($pdata['page']) ? 'Yes' : 'No') . "\n";
		echo "Table data exists: " . (isset($pdata['page']['table']) ? 'Yes' : 'No') . "\n";
		if(isset($pdata['page']['table'])) {
			echo "Table fields count: " . count($pdata['page']['table']) . "\n";
			echo "First 3 fields:\n";
			for($i = 0; $i < min(3, count($pdata['page']['table'])); $i++) {
				echo "  - " . $pdata['page']['table'][$i]['column_name'] . "\n";
			}
		}
		echo '</pre>';
	}
}
user_login_check();
// Handle item operations (add/update) - prevent double calls
if(isset($_POST['action']) || !empty($_POST['locationid']))
	add_item();
?>
<!doctype html>
<html>
<head>
	<title>Inventory Collection</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Optimized CSS for Full Screen Responsive Design -->
	<link rel="stylesheet" type="text/css" href="css/optimized.css">
	<script src="js/funcs.js"></script>
</head>
<body>
<form id="data_form" action="item.php?section=<?php echo $pdata['page']['section']; ?>" method="post" enctype="multipart/form-data">
	<input name="form-status" id="form-status" type="hidden" value="0" />
	<?php if(isset($_GET['iid'])) { ?>
	<input name="action" id="form-action" type="hidden" value="update" />
	<input name="iid" id="form-action" type="hidden" value="<?php echo $_GET['iid']; ?>" />
	<?php } else { ?>
	<input name="action" id="form-action" type="hidden" value="add" />
	<?php } ?>
	<input name="locationid" type="hidden" value="<?php echo $pdata['loc']['flo_code']; ?>" />
	<div class="content-wrapper">
		<?php include('lib/header.php'); ?>
		<!-- main content -->
		<section class="content">
			<?php if(isset($_GET['iid'])) { ?>
			<div class="row">
				<div class="col-md-12">
					<h1 class="h1" style="font-size: 90px; margin-top: -20px; padding-bottom: 60px;">item code: <?php echo $_GET['iid']; ?></h1>
				</div>
			</div>
			<?php } ?>
			<?php include('lib/form_btns.php'); ?>
			<br>
			<div class="row add-from">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Item Details</h3>
						</div>
						<div class="box-body">
							<?php 
							// Debug information
							if(!isset($pdata['page']['table']) || !is_array($pdata['page']['table'])) {
								echo '<div class="alert" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 15px;">';
								echo '<strong>Debug Info:</strong><br>';
								echo 'Section: ' . (isset($_GET['section']) ? $_GET['section'] : 'Not set') . '<br>';
								echo 'Page data initialized: ' . (isset($pdata['page']) ? 'Yes' : 'No') . '<br>';
								echo 'Table data: ' . (isset($pdata['page']['table']) ? 'Yes (' . count($pdata['page']['table']) . ' fields)' : 'No') . '<br>';
								if(isset($pdata['page']['table']) && !is_array($pdata['page']['table'])) {
									echo 'Table data type: ' . gettype($pdata['page']['table']) . '<br>';
								}
								echo '</div>';
							}
							
							// Generate form fields directly in PHP
							if(isset($pdata['page']['table']) && is_array($pdata['page']['table'])) {
								foreach($pdata['page']['table'] as $key => $col) {
									if(check_allowed_field($col['column_name'])) {
										$col['i'] = get_column_info($col);
										if(is_array($col['i'])) {
											$lname = (isset($col['i']['lname'])) ? $col['i']['lname'] : get_clean_column_name($col['i']['iname']);
											echo '<div class="form-group">';
											echo '<label for="' . htmlspecialchars($col['i']['iname']) . '">' . htmlspecialchars($lname) . ':</label>';
											
											// Generate appropriate input based on type
											$inputType = isset($col['i']['itype']) ? $col['i']['itype'] : 'text';
											$inputName = isset($col['i']['iname']) ? $col['i']['iname'] : '';
											$inputValue = isset($col['i']['value']) ? $col['i']['value'] : '';
											$isRequired = isset($col['i']['required']) ? $col['i']['required'] : false;
											
											switch($inputType) {
												case 'textarea':
													echo '<textarea class="form-control" name="' . htmlspecialchars($inputName) . '" id="' . htmlspecialchars($inputName) . '"';
													if($isRequired) echo ' required';
													echo '>' . htmlspecialchars($inputValue) . '</textarea>';
													break;
													
												case 'select':
													echo '<select class="form-control" name="' . htmlspecialchars($inputName) . '" id="' . htmlspecialchars($inputName) . '"';
													if($isRequired) echo ' required';
													echo '>';
													if(isset($col['i']['options']) && is_array($col['i']['options'])) {
														foreach($col['i']['options'] as $option) {
															$optValue = is_array($option) ? $option['value'] : $option;
															$optText = is_array($option) ? $option['text'] : $option;
															$selected = ($optValue == $inputValue) ? ' selected' : '';
															echo '<option value="' . htmlspecialchars($optValue) . '"' . $selected . '>' . htmlspecialchars($optText) . '</option>';
														}
													}
													echo '</select>';
													break;
													
												default:
													echo '<input type="' . htmlspecialchars($inputType) . '" class="form-control" name="' . htmlspecialchars($inputName) . '" id="' . htmlspecialchars($inputName) . '" value="' . htmlspecialchars($inputValue) . '"';
													if($isRequired) echo ' required';
													echo '>';
											}
											
											echo '</div>';
										}
									}
								}
							} else {
								echo '<p class="text-muted">No form fields configured for this section.</p>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row add-from">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Images</h3>
						</div>
						<div class="box-body">
							<div class="form-group" id="fupload">
								<div class="native-file-upload-area" id="fileUploadArea">
									<div class="upload-icon">📁</div>
									<div class="upload-text">
										<h3>Drag & Drop Images Here</h3>
										<p>or <span class="upload-browse">click to browse</span></p>
									</div>
									<input name="images[]" id="input_image" class="file-input-hidden" multiple type="file" accept="image/*">
									<div class="file-preview-container" id="filePreviewContainer"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="contect">
			<div class="row" style="text-align: center; width: 83%; margin: auto">
				<div class="col-xs-12">
					<?php if(isset($pdata['page']['item']) && gettype($pdata['page']['item']['images']) == 'array') foreach ($pdata['page']['item']['images'] as $key => $value) { ?>						
							<span class="image-container" data-image-id="<?php echo $value['fim_code']; ?>" style="width:300px; height: 200px; overflow: hidden; float:left; display: inline-block; padding: 2px; margin: 4px; position: relative; border: 2px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;"
							      onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.2)'; this.style.transform='scale(1.02)'"
							      onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'; this.style.transform='scale(1)'">
								<img src="lib/image.php?imgid=<?php echo $value['fim_code']; ?>" 
								     style="width: 100%; height: 100%; object-fit: cover; cursor: pointer; border-radius: 6px;" 
								     onclick="previewImage('<?php echo $value['fim_code']; ?>')"
								     title="Click to preview">
								<button type="button" 
								        onclick="removeImage('<?php echo $value['fim_code']; ?>', '<?php echo $pdata['page']['section']; ?>', '<?php echo $pdata['page']['iid']; ?>')" 
								        style="position: absolute; top: 8px; right: 8px; background: rgba(220,53,69,0.9); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; font-size: 16px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.3); transition: all 0.2s;"
								        title="Remove image"
								        onmouseover="this.style.background='rgba(220,53,69,1)'; this.style.transform='scale(1.1)'"
								        onmouseout="this.style.background='rgba(220,53,69,0.9)'; this.style.transform='scale(1)'">×</button>
							</span>
					<?php } ?>
				</div>
			</div>
		</section>
	</div>
</form>
<script>
// Form validation and interaction handled by native.js

// Image preview and removal functions
function previewImage(imageId) {
    var modal = document.getElementById('imagePreviewModal');
    var modalImg = document.getElementById('previewImg');
    modal.style.display = 'block';
    modalImg.src = 'lib/image.php?imgid=' + imageId;
}

function closePreview() {
    document.getElementById('imagePreviewModal').style.display = 'none';
}

function removeImage(imageId, section, iid) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Show loading indicator
        var imageContainer = document.querySelector('span img[src*="imgid=' + imageId + '"]').parentElement;
        var loadingOverlay = document.createElement('div');
        loadingOverlay.style.cssText = 'position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; border-radius: 6px;';
        loadingOverlay.innerHTML = 'Removing...';
        imageContainer.appendChild(loadingOverlay);
        
        // Send AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'lib/remove_image_ajax.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        // Remove the image container with animation
                        imageContainer.style.transition = 'all 0.3s ease-out';
                        imageContainer.style.transform = 'scale(0)';
                        imageContainer.style.opacity = '0';
                        
                        setTimeout(function() {
                            imageContainer.remove();
                        }, 300);
                        
                        // Show success message briefly
                        showMessage('Image removed successfully', 'success');
                    } else {
                        // Remove loading overlay and show error
                        imageContainer.removeChild(loadingOverlay);
                        showMessage('Error: ' + (response.error || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    // Remove loading overlay and show error
                    imageContainer.removeChild(loadingOverlay);
                    showMessage('Error: Invalid server response', 'error');
                }
            }
        };
        
        // Send the request
        var params = 'action=remove_image&image_id=' + encodeURIComponent(imageId);
        xhr.send(params);
    }
}

// Helper function to show messages
function showMessage(message, type) {
    var messageDiv = document.createElement('div');
    messageDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; padding: 15px 20px; border-radius: 5px; color: white; font-weight: bold; z-index: 10000; transition: all 0.3s ease;';
    messageDiv.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    // Fade out and remove after 3 seconds
    setTimeout(function() {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateX(100%)';
        setTimeout(function() {
            if (messageDiv.parentElement) {
                messageDiv.parentElement.removeChild(messageDiv);
            }
        }, 300);
    }, 3000);
}

// Close modal when clicking outside the image
window.onclick = function(event) {
    var modal = document.getElementById('imagePreviewModal');
    if (event.target == modal) {
        closePreview();
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        var modal = document.getElementById('imagePreviewModal');
        if (modal.style.display === 'block') {
            closePreview();
        }
    }
});

// Auto-focus on first input when page loads
document.addEventListener('DOMContentLoaded', function() {
    const firstInput = document.querySelector('input[type="text"], input[type="number"], textarea, select');
    if (firstInput) {
        firstInput.focus();
    }
});
</script>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); overflow: auto;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 95%; max-height: 95%; text-align: center;">
        <img id="previewImg" style="width: auto; height: auto; max-width: 100%; max-height: 90vh; border: 2px solid #fff; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
        <br>
        <button onclick="closePreview()" 
                style="margin-top: 15px; background: #fff; color: #333; border: 2px solid #333; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer; font-weight: bold;"
                title="Close Preview">Close</button>
        <!-- Alternative close button in corner -->
        <button onclick="closePreview()" 
                style="position: absolute; top: -10px; right: -10px; background: rgba(255,255,255,0.9); color: #333; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 20px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 10px rgba(0,0,0,0.3);"
                title="Close">×</button>
    </div>
</div>

</body>
</html>