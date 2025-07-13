document.getElementById('password').addEventListener('input', function () {
  const password = this.value;
  const strengthBar = document.getElementById('strength-bar');
  const strengthMessage = document.getElementById('strength-message');
  
  // Regular expressions for checking different password criteria
  const regexLength = /.{8,}/;  // At least 8 characters
  const regexUppercase = /[A-Z]/;  // Uppercase letter
  const regexLowercase = /[a-z]/;  // Lowercase letter
  const regexDigit = /\d/;  // At least one digit
  const regexSpecial = /[!@#$%^&*(),.?":{}|<>]/;  // Special characters

  let strength = 0;

  // Check length (optional)
  if (regexLength.test(password)) strength++;

  // Check for uppercase letters
  if (regexUppercase.test(password)) strength++;

  // Check for digits
  if (regexDigit.test(password)) strength++;

  // Check for special characters
  if (regexSpecial.test(password)) strength++;

  // Check if all 3 required conditions are met for strong password
  if (regexLength.test(password) &&  regexLowercase.test(password) && regexUppercase.test(password) && regexDigit.test(password) && regexSpecial.test(password)) {
      strength = 5;  // Strong password if it contains all three
  }

  // Set strength bar width and color based on strength
  if (strength === 1) {
      strengthBar.style.width = '20%';
      strengthBar.className = 'strength-bar weak';
      strengthMessage.textContent = '弱い';  // Weak
      strengthMessage.className = 'weak';
  } else if (strength >= 2 && strength < 5) {
      strengthBar.style.width = '50%';
      strengthBar.className = 'strength-bar medium';
      strengthMessage.textContent = '普通';  // Medium
      strengthMessage.className = 'medium';
  } else if (strength >= 5) {
      strengthBar.style.width = '100%';
      strengthBar.className = 'strength-bar strong';
      strengthMessage.textContent = '強い';  // Strong
      strengthMessage.className = 'strong';
  } else {
      strengthBar.style.width = '0%';
      strengthBar.className = 'strength-bar';
      strengthMessage.textContent = '';
  }
});
