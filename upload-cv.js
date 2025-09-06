document.getElementById("cvForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = e.target;
  const fileInput = document.getElementById("cv");
  const statusMessage = document.getElementById("status-message");

  if (fileInput.files.length === 0) {
    alert("Please upload a CV file before submitting.");
    return;
  }

  const reader = new FileReader();
  reader.onload = function(event) {
    const base64File = event.target.result;

    emailjs.send("service_yp6w9bl", "template_b8fw9ng", {
      from_name: form.from_name.value,
      from_email: form.from_email.value,
      position: form.position.value,
      cv_file: base64File
    })
    .then(() => {
      statusMessage.textContent = "✅ CV uploaded successfully! We will contact you soon.";
      form.reset();
    }, (error) => {
      statusMessage.textContent = "❌ Failed to send. Please try again later.";
      console.error("EmailJS Error:", error);
    });
  };
  reader.readAsDataURL(fileInput.files[0]);
});
