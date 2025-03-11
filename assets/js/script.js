// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Auto-dismiss alerts after 5 seconds
  setTimeout(() => {
    const alerts = document.querySelectorAll(".alert")
    alerts.forEach((alert) => {
      // Ensure bootstrap is available before using it
      if (typeof bootstrap !== "undefined") {
        const bsAlert = new bootstrap.Alert(alert)
        bsAlert.close()
      } else {
        console.warn("Bootstrap is not defined. Alert auto-dismiss may not work.")
        // Optionally, implement a fallback to close the alert without Bootstrap
        alert.style.display = "none"
      }
    })
  }, 5000)

  // Initialize TinyMCE if the element exists
  if (document.getElementById("content")) {
    // TinyMCE is loaded via CDN in the specific pages that need it
  }

  // Add active class to current nav item
  const currentLocation = window.location.pathname
  const navLinks = document.querySelectorAll(".navbar-nav .nav-link")

  navLinks.forEach((link) => {
    const linkPath = link.getAttribute("href")
    if (linkPath === currentLocation || (linkPath !== "index.php" && currentLocation.includes(linkPath))) {
      link.classList.add("active")
    }
  })

  // Confirm delete actions
  const deleteButtons = document.querySelectorAll(".delete-confirm")
  deleteButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!confirm("Are you sure you want to delete this item? This action cannot be undone.")) {
        e.preventDefault()
      }
    })
  })

  // Image preview for upload
  const imageInput = document.getElementById("image")
  const imagePreview = document.getElementById("image-preview")

  if (imageInput && imagePreview) {
    imageInput.addEventListener("change", function () {
      if (this.files && this.files[0]) {
        const reader = new FileReader()

        reader.onload = (e) => {
          imagePreview.src = e.target.result
          imagePreview.style.display = "block"
        }

        reader.readAsDataURL(this.files[0])
      }
    })
  }

  // Password strength meter
  const passwordInput = document.getElementById("new_password")
  const passwordStrength = document.getElementById("password-strength")

  if (passwordInput && passwordStrength) {
    passwordInput.addEventListener("input", function () {
      const password = this.value
      let strength = 0

      if (password.length >= 8) strength += 1
      if (password.match(/[a-z]+/)) strength += 1
      if (password.match(/[A-Z]+/)) strength += 1
      if (password.match(/[0-9]+/)) strength += 1
      if (password.match(/[^a-zA-Z0-9]+/)) strength += 1

      switch (strength) {
        case 0:
        case 1:
          passwordStrength.className = "progress-bar bg-danger"
          passwordStrength.style.width = "20%"
          passwordStrength.textContent = "Very Weak"
          break
        case 2:
          passwordStrength.className = "progress-bar bg-warning"
          passwordStrength.style.width = "40%"
          passwordStrength.textContent = "Weak"
          break
        case 3:
          passwordStrength.className = "progress-bar bg-info"
          passwordStrength.style.width = "60%"
          passwordStrength.textContent = "Medium"
          break
        case 4:
          passwordStrength.className = "progress-bar bg-primary"
          passwordStrength.style.width = "80%"
          passwordStrength.textContent = "Strong"
          break
        case 5:
          passwordStrength.className = "progress-bar bg-success"
          passwordStrength.style.width = "100%"
          passwordStrength.textContent = "Very Strong"
          break
      }
    })
  }
})

