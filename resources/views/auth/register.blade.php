<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="register-form">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Tên')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <p id="name-error" class="text-red-600 text-sm mt-1"></p>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <p id="email-error" class="text-red-600 text-sm mt-1"></p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <p id="password-error" class="text-red-600 text-sm mt-1 hidden"></p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Đã có tài khoản?') }}
            </a>

            <x-primary-button class="ml-4" id="register-button">
                {{ __('Đăng ký') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Xác thực tên không chứa số và ký tự đặc biệt như !@#$%^&*()
        function validateName() {
            const name = document.getElementById("name").value;
            const nameError = document.getElementById("name-error");
            const regex = /^[a-zA-ZÀ-ỹ\s]+$/u;
            const specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/;

            if (specialChars.test(name) || !regex.test(name)) {
                nameError.style.display = "block";
                nameError.innerText = "Tên không được chứa số hoặc ký tự đặc biệt";
                return false;
            } else {
                nameError.style.display = "none";
                nameError.innerText = "";
            }
            return true;
        }

        // Xác thực email không chứa ký tự đặc biệt ngoài @
        function validateEmail() {
            const email = document.getElementById("email").value;
            const emailError = document.getElementById("email-error");
            const regex = /^[A-Za-z0-9@._]+$/;
            const specialChars = /[!#$%^&*()+=\[\]{};':"\\|,<>\/?~`-]/;

            if (specialChars.test(email) || !regex.test(email)) {
                emailError.style.display = "block";
                emailError.innerText = "Email không hợp lệ";
                return false;
            } else {
                emailError.style.display = "none";
                emailError.innerText = "";
            }
            return true;
        }

        // Gắn sự kiện kiểm tra tên và email khi người dùng nhập
        document.getElementById("name").addEventListener("input", validateName);
        document.getElementById("email").addEventListener("input", validateEmail);

        // Kiểm tra độ mạnh của mật khẩu
        function checkPasswordStrength() {
            const password = document.getElementById("password").value;
            const passwordError = document.getElementById("password-error");
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            if (!regex.test(password)) {
                passwordError.classList.remove("hidden");
                passwordError.innerText =
                    "Mật khẩu phải có ít nhất 8 ký tự, bao gồm ít nhất 1 chữ thường, 1 chữ in hoa, 1 số và 1 ký tự đặc biệt.";
            } else {
                passwordError.classList.add("hidden");
                passwordError.innerText = "";
            }
        }

        // Xác thực mật khẩu khi gửi form
        function validatePassword() {
            const password = document.getElementById("password").value;
            const passwordError = document.getElementById("password-error");
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            if (!regex.test(password)) {
                passwordError.classList.remove("hidden");
                passwordError.innerText =
                    "Mật khẩu phải có ít nhất 8 ký tự, bao gồm ít nhất 1 chữ thường, 1 chữ in hoa, 1 số và 1 ký tự đặc biệt.";
                return false;
            }

            return true;
        }

        // Gắn sự kiện kiểm tra độ mạnh mật khẩu khi người dùng nhập
        document.getElementById("password").addEventListener("input", function() {
            checkPasswordStrength();
            validatePassword();
        });

        // Đảm bảo phần tử lỗi mật khẩu ẩn ban đầu
        document.getElementById("password-error").classList.add("hidden");

        // Gắn sự kiện xác thực khi submit form
        document.getElementById("register-form").addEventListener("submit", function(event) {
            if (!validateName() || !validateEmail() || !validatePassword()) {
                event.preventDefault();
            }
        });
    </script>
</x-guest-layout>
