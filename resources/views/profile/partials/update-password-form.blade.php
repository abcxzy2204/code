<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Cập nhật mật khẩu') }}
        </h2>

            <p class="mt-1 text-sm text-gray-600">
        {{ __('Đảm bảo tài khoản của bạn sử dụng mật khẩu dài và ngẫu nhiên để đảm bảo an toàn.') }}
    </p>
</header>

<form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6" id="update-password-form">
    @csrf
    @method('put')

    <div>
        <x-input-label for="current_password" :value="__('Mật khẩu hiện tại')" />
        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Mật khẩu mới')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <p id="password-error" class="text-red-600 text-sm mt-1 hidden"></p>
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Lưu') }}</x-primary-button>

        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600"
            >{{ __('Đã lưu.') }}</p>
        @endif
    </div>
</form>

<script>
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
        } else {
            passwordError.classList.add("hidden");
            passwordError.innerText = "";
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
    document.getElementById("update-password-form").addEventListener("submit", function(event) {
        if (!validatePassword()) {
            event.preventDefault();
        }
    });
</script>

</section>
