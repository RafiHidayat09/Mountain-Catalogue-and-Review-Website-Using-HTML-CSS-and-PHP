const SignUpButton = document.getElementById('signUpButton');
const SignInButton = document.getElementById('signInButton');
const SignInForm = document.getElementById('signin');
const SignUpForm = document.getElementById('signup');

SignUpButton.addEventListener('click', function () {
    SignInForm.style.display = "none";
    SignUpForm.style.display = "block";
});

SignInButton.addEventListener('click', function () {
    SignInForm.style.display = "block";
    SignUpForm.style.display = "none";
});