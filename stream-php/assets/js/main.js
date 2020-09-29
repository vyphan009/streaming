$(document).ready(function (e) {
  $("#deleteForm").on('submit', (function(e) {
    if ($(this).find('input[type=checkbox]:checked').length == 0) {
      alert("Select file to be deleted");
      return false;
    }
    else if (confirm('Are you sure you want to delete selected file?')) {
      return true;
    }
    return false;
  }));
});
