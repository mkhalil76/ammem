$(document).ready(function() {
    $('#my-form').validate({
        rules: {
          username: {
            required: true,
          },
          action: "required"
        },
        messages: {
          username: {
            required: "Please enter some data",
            minlength: "Your data must be at least 8 characters"
          },
          action: "Please provide some data"
        }
    });
});