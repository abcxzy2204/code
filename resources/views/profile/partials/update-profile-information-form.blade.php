<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Thông tin hồ sơ') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Cập nhật thông tin hồ sơ tài khoản và địa chỉ email của bạn.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" id="update-profile-form">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Tên')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <p id="name-error" class="text-red-600 text-sm mt-1 hidden"></p>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <p id="email-error" class="text-red-600 text-sm mt-1 hidden"></p>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Địa chỉ email của bạn chưa được xác minh.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Nhấp vào đây để gửi lại email xác minh.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Một liên kết xác minh mới đã được gửi đến địa chỉ email của bạn.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Lưu') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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
        // Xác thực tên không chứa số và ký tự đặc biệt như !@#$%^&*()
        function validateName() {
            const name = document.getElementById("name").value;
            const nameError = document.getElementById("name-error");
            const regex = /^[a-zA-ZÀ-ỹ\s]+$/u;
            const specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/;

            if (specialChars.test(name) || !regex.test(name)) {
                nameError.style.display = "block";
                nameError.innerText = "Tên không được chứa số hay ký tự đặc biệt";
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

        // Gắn sự kiện xác thực khi submit form
        document.getElementById("update-profile-form").addEventListener("submit", function(event) {
            if (!validateName() || !validateEmail()) {
                event.preventDefault();
            }
        });
    </script>
</section>
