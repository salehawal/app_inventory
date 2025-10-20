/**
 * Native Drag & Drop File Upload Implementation
 * Pure JavaScript with no external dependencies
 */

// Initialize drag and drop upload when DOM is ready
function initializeDragDropUpload() {
    const uploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('input_image');
    const previewContainer = document.getElementById('filePreviewContainer');
    
    if (!uploadArea || !fileInput) return;
    
    let selectedFiles = [];
    
    // Click to browse functionality
    uploadArea.addEventListener('click', function(e) {
        if (e.target.classList.contains('file-preview-remove')) return;
        fileInput.click();
    });
    
    // File input change event
    fileInput.addEventListener('change', function(e) {
        handleFiles(Array.from(e.target.files));
    });
    
    // Drag and drop events
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!uploadArea.contains(e.relatedTarget)) {
            uploadArea.classList.remove('drag-over');
        }
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files);
        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        
        if (imageFiles.length !== files.length) {
            showMessage('Only image files are allowed', 'warning');
        }
        
        if (imageFiles.length > 0) {
            handleFiles(imageFiles);
        }
    });
    
    function handleFiles(files) {
        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    showMessage(`File "${file.name}" is too large. Maximum size is 10MB.`, 'error');
                    return;
                }
                
                selectedFiles.push(file);
                createFilePreview(file);
            } else {
                showMessage(`File "${file.name}" is not an image.`, 'error');
            }
        });
        
        updateFileInput();
    }
    
    function createFilePreview(file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            previewItem.dataset.fileName = file.name;
            
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="${file.name}" class="file-preview-image">
                <button type="button" class="file-preview-remove" onclick="removeFilePreview('${file.name}')">&times;</button>
                <div class="file-info">${file.name} (${formatFileSize(file.size)})</div>
            `;
            
            previewContainer.appendChild(previewItem);
        };
        
        reader.onerror = function() {
            showMessage(`Error reading file "${file.name}"`, 'error');
        };
        
        reader.readAsDataURL(file);
    }
    
    function updateFileInput() {
        // Create a new DataTransfer object to update the file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        
        // Update upload area display
        if (selectedFiles.length > 0) {
            const uploadText = uploadArea.querySelector('.upload-text');
            const uploadIcon = uploadArea.querySelector('.upload-icon');
            if (uploadText && uploadIcon) {
                uploadIcon.textContent = '✓';
                uploadText.querySelector('h3').textContent = `${selectedFiles.length} file(s) selected`;
                uploadText.querySelector('p').innerHTML = 'Drag more files here or <span class="upload-browse">click to browse</span>';
            }
        }
    }
    
    // Global function to remove file preview
    window.removeFilePreview = function(fileName) {
        // Remove from selectedFiles array
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);
        
        // Remove preview element
        const previewItem = previewContainer.querySelector(`[data-file-name="${fileName}"]`);
        if (previewItem) {
            previewItem.remove();
        }
        
        // Update file input
        updateFileInput();
        
        // Reset upload area display if no files
        if (selectedFiles.length === 0) {
            const uploadText = uploadArea.querySelector('.upload-text');
            const uploadIcon = uploadArea.querySelector('.upload-icon');
            if (uploadText && uploadIcon) {
                uploadIcon.textContent = '📁';
                uploadText.querySelector('h3').textContent = 'Drag & Drop Images Here';
                uploadText.querySelector('p').innerHTML = 'or <span class="upload-browse">click to browse</span>';
            }
        }
    };
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Show messages function (fallback if not available)
    function showMessage(message, type) {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeDragDropUpload();
});