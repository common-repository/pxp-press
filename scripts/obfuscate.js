// Obfuscate the Cloudfront Secret Key when not focused
document.addEventListener("DOMContentLoaded", function() {
  var secretInput = document.getElementById("secretKey");

  secretInput.addEventListener("click", function() {
    this.select();
  });
  secretInput.addEventListener("input", function() {
    this.setAttribute("type", "text");
  });
  secretInput.addEventListener("blur", function() {
    this.setAttribute("type", "password");
  });
});
