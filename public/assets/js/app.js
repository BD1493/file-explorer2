function toggleShareFields() {
    var isPublic = document.getElementById('is_public').checked;
    var privateFields = document.getElementById('private_fields');
    if (isPublic) {
        privateFields.style.display = 'none';
        document.getElementById('share_name').required = false;
        document.getElementById('share_pass').required = false;
    } else {
        privateFields.style.display = 'block';
        document.getElementById('share_name').required = true;
    }
}
