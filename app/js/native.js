/**
 * Native JavaScript Replacement for jQuery functionality
 * Removes dependency on external libraries
 */

// DOM Ready replacement for $(document).ready()
function domReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
}

// Native DOM manipulation utilities
const Native = {
    // Element selection
    select: function(selector) {
        if (selector.startsWith('#')) {
            return document.getElementById(selector.slice(1));
        }
        return document.querySelector(selector);
    },

    selectAll: function(selector) {
        return document.querySelectorAll(selector);
    },

    // Event handling
    on: function(element, event, callback) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element) {
            element.addEventListener(event, callback);
        }
    },

    // Delegate event handling (replacement for $(document).on())
    delegate: function(parentSelector, childSelector, event, callback) {
        const parent = this.select(parentSelector);
        if (parent) {
            parent.addEventListener(event, function(e) {
                if (e.target.matches(childSelector)) {
                    callback.call(e.target, e);
                }
            });
        }
    },

    // Attribute manipulation
    attr: function(element, attribute, value) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (!element) return;

        if (value === undefined) {
            return element.getAttribute(attribute);
        } else {
            element.setAttribute(attribute, value);
        }
    },

    // Value manipulation
    val: function(element, value) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (!element) return;

        if (value === undefined) {
            return element.value;
        } else {
            element.value = value;
        }
    },

    // Property checking
    is: function(element, property) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (!element) return false;

        switch (property) {
            case ':enabled':
                return !element.disabled;
            case ':disabled':
                return element.disabled;
            case ':hidden':
                return element.style.display === 'none' || 
                       element.hidden || 
                       element.offsetParent === null;
            case ':visible':
                return !this.is(element, ':hidden');
            default:
                return false;
        }
    },

    // CSS class manipulation
    addClass: function(element, className) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element) {
            element.classList.add(className);
        }
    },

    removeClass: function(element, className) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element) {
            element.classList.remove(className);
        }
    },

    // Show/hide elements
    show: function(element) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element) {
            element.style.display = '';
        }
    },

    hide: function(element) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element) {
            element.style.display = 'none';
        }
    },

    // Each iteration (replacement for $.each)
    each: function(elements, callback) {
        if (typeof elements === 'string') {
            elements = this.selectAll(elements);
        }
        
        if (elements && elements.length !== undefined) {
            Array.from(elements).forEach(callback);
        }
    },

    // Find elements within parent
    find: function(parent, selector) {
        if (typeof parent === 'string') {
            parent = this.select(parent);
        }
        if (parent) {
            return parent.querySelector(selector);
        }
        return null;
    },

    findAll: function(parent, selector) {
        if (typeof parent === 'string') {
            parent = this.select(parent);
        }
        if (parent) {
            return parent.querySelectorAll(selector);
        }
        return [];
    },

    // Click simulation
    click: function(element) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element) {
            element.click();
        }
    },

    // Remove element
    remove: function(element) {
        if (typeof element === 'string') {
            element = this.select(element);
        }
        if (element && element.parentNode) {
            element.parentNode.removeChild(element);
        }
    }
};

// Native file input handling
function setupFileInput() {
    // Replace $(document).on('change', '.btn-file :file', function() {
    Native.delegate('document', '.btn-file input[type="file"]', 'change', function() {
        const input = this;
        const numFiles = input.files ? input.files.length : 1;
        const label = input.value.replace(/\\/g, '/').replace(/.*\//, '');
        
        const fileLabel = input.closest('.input-group').querySelector('.form-control[readonly]');
        if (fileLabel) {
            fileLabel.value = numFiles > 1 ? numFiles + ' files selected' : label;
        }
    });
}

// Native form validation functions
function checkForm() {
    const formElements = Native.selectAll('#data_form input, #data_form textarea, #data_form select');
    let status = false;
    
    Native.each(formElements, function(element) {
        if (Native.is(element, ':enabled') && !Native.is(element, ':hidden')) {
            const value = Native.val(element);
            if (value !== "" && value !== "--" && Native.is(element, ':enabled')) {
                status = true;
                // Form validation logic here
                // console.log(element.type + ' - ' + element.id + '  -  ' + value);
            }
        }
    });
    
    if (status)
        enable_submit();
    else
        disable_submit();
}

function enable_validation() {
    const formElements = Native.selectAll('#data_form input, #data_form textarea, #data_form select');
    
    Native.each(formElements, function(element) {
        if (Native.is(element, ':enabled')) {
            Native.attr(element, 'onKeyDown', 'javascript:checkForm();');
            Native.attr(element, 'onChange', 'javascript:checkForm();');
        }
    });
}

function submit_form() {
    const form = Native.select('#data_form');
    const formStatus = Native.select('#form-status');
    
    // Ensure form status is set to 1 before submission
    if (formStatus) {
        Native.val(formStatus, '1');
    }
    
    if (form) {
        form.submit();
    }
}

function disable_submit() {
    const btnOk = Native.select('#btn-ok');
    if (btnOk) {
        btnOk.className = 'btn btn-info btn-info-disable form-menu-btn';
        btnOk.disabled = true;
    }
    const formStatus = Native.select('#form-status');
    if (formStatus) {
        Native.val(formStatus, '0');
    }
}

function enable_submit() {
    const btnOk = Native.select('#btn-ok');
    if (btnOk) {
        btnOk.className = 'btn btn-info form-menu-btn';
        btnOk.disabled = false;
    }
    const formStatus = Native.select('#form-status');
    if (formStatus) {
        Native.val(formStatus, '1');
    }
}

function enable_switch() {
    // Native implementation for checkbox/radio styling
    const formInputs = Native.selectAll('#data_form input');
    Native.each(formInputs, function(input) {
        if (input.type === 'radio' || input.type === 'checkbox') {
            // Add native styling class for switches
            Native.addClass(input, 'native-switch');
            
            // Wrap in switch container if not already wrapped
            if (!input.parentElement.classList.contains('switch-container')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'switch-container';
                input.parentElement.insertBefore(wrapper, input);
                wrapper.appendChild(input);
                
                const label = document.createElement('label');
                label.className = 'switch-label';
                label.setAttribute('for', input.id);
                wrapper.appendChild(label);
            }
        }
    });
}

// Native modal functionality (replacement for Bootstrap modals)
function showImageModal(imageSrc) {
    // Remove existing modal if any
    const existingModal = Native.select('#imageModal');
    if (existingModal) {
        Native.remove(existingModal);
    }

    // Create modal HTML
    const modalHTML = `
        <div id="imageModal" class="native-modal">
            <div class="native-modal-backdrop" onclick="closeImageModal()"></div>
            <div class="native-modal-content">
                <div class="native-modal-header">
                    <button type="button" class="native-close" onclick="closeImageModal()">&times;</button>
                </div>
                <div class="native-modal-body">
                    <img src="${imageSrc}" alt="Image Preview" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    `;

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal
    const modal = Native.select('#imageModal');
    modal.style.display = 'flex';
    
    // Focus trap for accessibility
    modal.querySelector('.native-close').focus();
}

function closeImageModal() {
    const modal = Native.select('#imageModal');
    if (modal) {
        Native.remove(modal);
    }
}

// File input enhancement (replacement for Bootstrap file input)
function enhanceFileInput() {
    const fileDropZone = Native.find('div', '.file-drop-zone');
    if (fileDropZone) {
        Native.attr(fileDropZone, 'id', 'filebox');
        
        Native.on('#filebox', 'click', function() {
            Native.click('#input_image');
        });
    }
}

// Initialize everything when DOM is ready
domReady(function() {
    enhanceFileInput();
    
    // Only initialize form validation if we're on the item page
    if (document.getElementById('data_form')) {
        enable_validation();
        enable_switch();
        disable_submit();
        
        // Check form immediately for existing items with data
        setTimeout(function() {
            checkForm();
        }, 100);
    }
    
    // Initialize drag and drop file upload
    initializeDragDropUpload();
    
    // Setup escape key for modal closing
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
    
    // Initialize DataTables if they exist
    initializeDataTables();
});

// Native DataTable implementation
function initializeDataTables() {
    const tables = document.querySelectorAll('table.datatable, #example1');
    tables.forEach(function(table) {
        if (!table.classList.contains('native-datatable-initialized')) {
            createNativeDataTable(table);
        }
    });
}

function createNativeDataTable(table) {
    table.classList.add('native-datatable', 'native-datatable-initialized');
    
    // Create wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'native-datatable-wrapper';
    table.parentNode.insertBefore(wrapper, table);
    
    // Create controls
    const controls = document.createElement('div');
    controls.className = 'datatable-controls';
    controls.innerHTML = `
        <div class="datatable-length">
            Show <select onchange="changePageSize(this, '${table.id || 'table'}')">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select> entries
        </div>
        <div class="datatable-search">
            <input type="text" placeholder="Search..." onkeyup="searchTable(this, '${table.id || 'table'}')">
        </div>
    `;
    
    wrapper.appendChild(controls);
    wrapper.appendChild(table);
    
    // Create info and pagination
    const footer = document.createElement('div');
    footer.innerHTML = `
        <div class="datatable-info" id="info-${table.id || 'table'}"></div>
        <div class="datatable-pagination" id="pagination-${table.id || 'table'}"></div>
    `;
    wrapper.appendChild(footer);
    
    // Make headers sortable
    const headers = table.querySelectorAll('thead th');
    headers.forEach(function(header, index) {
        header.classList.add('sortable');
        header.onclick = function() {
            sortTable(table, index);
        };
    });
    
    // Initialize pagination
    updatePagination(table);
    updateInfo(table);
}

function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const header = table.querySelectorAll('thead th')[columnIndex];
    
    // Determine sort direction
    let isAsc = !header.classList.contains('sort-asc');
    
    // Clear all sort classes
    table.querySelectorAll('thead th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Add appropriate sort class
    header.classList.add(isAsc ? 'sort-asc' : 'sort-desc');
    
    // Sort rows
    rows.sort(function(a, b) {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();
        
        // Try numeric comparison first
        const aNum = parseFloat(aText);
        const bNum = parseFloat(bText);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAsc ? aNum - bNum : bNum - aNum;
        }
        
        // Text comparison
        return isAsc ? 
            aText.localeCompare(bText) : 
            bText.localeCompare(aText);
    });
    
    // Rebuild tbody
    rows.forEach(row => tbody.appendChild(row));
    
    updatePagination(table);
}

function searchTable(input, tableId) {
    const table = tableId === 'table' ? 
        input.closest('.native-datatable-wrapper').querySelector('table') :
        document.getElementById(tableId);
    
    const filter = input.value.toLowerCase();
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        const shouldShow = text.includes(filter);
        row.style.display = shouldShow ? '' : 'none';
        row.dataset.visible = shouldShow ? 'true' : 'false';
    });
    
    updatePagination(table);
    updateInfo(table);
}

function changePageSize(select, tableId) {
    const table = tableId === 'table' ? 
        select.closest('.native-datatable-wrapper').querySelector('table') :
        document.getElementById(tableId);
    
    table.dataset.pageSize = select.value;
    updatePagination(table);
}

function updatePagination(table) {
    const pageSize = parseInt(table.dataset.pageSize || '10');
    const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => 
        row.dataset.visible !== 'false'
    );
    const totalPages = Math.ceil(visibleRows.length / pageSize);
    const currentPage = parseInt(table.dataset.currentPage || '1');
    
    // Show/hide rows for current page
    visibleRows.forEach(function(row, index) {
        const pageNum = Math.floor(index / pageSize) + 1;
        row.style.display = pageNum === currentPage ? '' : 'none';
    });
    
    // Update pagination controls
    const paginationId = `pagination-${table.id || 'table'}`;
    const pagination = document.getElementById(paginationId);
    
    if (pagination && totalPages > 1) {
        let html = '';
        
        // Previous button
        html += `<button onclick="goToPage('${table.id || 'table'}', ${currentPage - 1})" 
                 ${currentPage <= 1 ? 'disabled' : ''}>Previous</button>`;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `<button onclick="goToPage('${table.id || 'table'}', ${i})" 
                         ${i === currentPage ? 'class="active"' : ''}>${i}</button>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += '<span>...</span>';
            }
        }
        
        // Next button
        html += `<button onclick="goToPage('${table.id || 'table'}', ${currentPage + 1})" 
                 ${currentPage >= totalPages ? 'disabled' : ''}>Next</button>`;
        
        pagination.innerHTML = html;
    }
    
    updateInfo(table);
}

function goToPage(tableId, page) {
    const table = tableId === 'table' ? 
        document.querySelector('.native-datatable-wrapper table') :
        document.getElementById(tableId);
    
    const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => 
        row.dataset.visible !== 'false'
    );
    const pageSize = parseInt(table.dataset.pageSize || '10');
    const totalPages = Math.ceil(visibleRows.length / pageSize);
    
    if (page >= 1 && page <= totalPages) {
        table.dataset.currentPage = page;
        updatePagination(table);
    }
}

function updateInfo(table) {
    const pageSize = parseInt(table.dataset.pageSize || '10');
    const currentPage = parseInt(table.dataset.currentPage || '1');
    const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => 
        row.dataset.visible !== 'false'
    );
    const totalRows = table.querySelectorAll('tbody tr').length;
    
    const start = Math.min((currentPage - 1) * pageSize + 1, visibleRows.length);
    const end = Math.min(currentPage * pageSize, visibleRows.length);
    
    const infoId = `info-${table.id || 'table'}`;
    const info = document.getElementById(infoId);
    
    if (info) {
        if (visibleRows.length === totalRows) {
            info.textContent = `Showing ${start} to ${end} of ${totalRows} entries`;
        } else {
            info.textContent = `Showing ${start} to ${end} of ${visibleRows.length} entries (filtered from ${totalRows} total entries)`;
        }
    }
}

// Native Drag & Drop File Upload Implementation
function initializeDragDropUpload() {
    const uploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('input_image');
    const previewContainer = document.getElementById('filePreviewContainer');
    
    if (!uploadArea || !fileInput) return;
    
    // Prevent multiple initializations
    if (fileInput.dataset.initialized === 'true') return;
    fileInput.dataset.initialized = 'true';
    
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
            showNotification('Only image files are allowed', 'warning');
        }
        
        if (imageFiles.length > 0) {
            handleFiles(imageFiles);
        }
    });
    
    function handleFiles(files) {
        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    showNotification(`File "${file.name}" is too large. Maximum size is 10MB.`, 'error');
                    return;
                }
                
                selectedFiles.push(file);
                createFilePreview(file);
            } else {
                showNotification(`File "${file.name}" is not an image.`, 'error');
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
            showNotification(`Error reading file "${file.name}"`, 'error');
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
}

// Existing AJAX image removal function (already native)
function removeImage(imageId, section, iid) {
    if (!confirm('Are you sure you want to remove this image?')) {
        return;
    }

    // Show loading state
    const imageElement = document.querySelector(`[onclick*="${imageId}"]`);
    if (imageElement) {
        const container = imageElement.closest('.image-container') || imageElement.closest('div');
        if (container) {
            container.style.opacity = '0.5';
        }
    }

    // Prepare form data
    const params = 'image_id=' + encodeURIComponent(imageId) + 
                  '&section=' + encodeURIComponent(section) + 
                  '&iid=' + encodeURIComponent(iid);

    // Create and send AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'lib/remove_image_ajax.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 200 && response.success) {
                    // Remove the image element from DOM
                    if (imageElement) {
                        const container = imageElement.closest('.image-container') || 
                                        imageElement.closest('div') || 
                                        imageElement.parentElement;
                        if (container) {
                            container.style.transition = 'opacity 0.3s ease';
                            container.style.opacity = '0';
                            setTimeout(() => {
                                Native.remove(container);
                            }, 300);
                        } else {
                            Native.remove(imageElement);
                        }
                    }
                    
                    // Show success message
                    if (response.message) {
                        showNotification(response.message, 'success');
                    }
                } else {
                    // Reset opacity on error
                    if (imageElement) {
                        const container = imageElement.closest('.image-container') || imageElement.closest('div');
                        if (container) {
                            container.style.opacity = '1';
                        }
                    }
                    
                    // Show error message
                    const errorMsg = response.message || 'Failed to remove image';
                    showNotification(errorMsg, 'error');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showNotification('An error occurred while removing the image', 'error');
                
                // Reset opacity on error
                if (imageElement) {
                    const container = imageElement.closest('.image-container') || imageElement.closest('div');
                    if (container) {
                        container.style.opacity = '1';
                    }
                }
            }
        }
    };
    
    xhr.send(params);
}

// Native notification system (replacement for Bootstrap alerts)
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = Native.selectAll('.native-notification');
    Native.each(existing, function(notification) {
        Native.remove(notification);
    });

    // Create notification
    const notification = document.createElement('div');
    notification.className = `native-notification native-notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button type="button" onclick="Native.remove(this.parentElement)">&times;</button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            Native.remove(notification);
        }
    }, 5000);
}