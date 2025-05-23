<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - CrudApi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            display: flex;
            width: 1200px;
            background-color: #fff;
            border: 1px solid #3b3b3b;
            border-radius: 10px;
            overflow: hidden;
        }

        .left-panel {
            width: 30%;
            background: linear-gradient(to bottom right, #4a86f7, #0051ff);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 30px;
            color: #fff;
            text-align: center;
        }

        .left-panel h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .left-panel p {
            font-size: 16px;
            opacity: 0.8;
        }

        .right-panel {
            width: 70%;
            padding: 40px;
        }

        .right-panel h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
            flex: 1;
        }

        .form-group .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #999;
        }

        .form-group input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: none;
            border-bottom: 2px solid #ccc;
            background: transparent;
            outline: none;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #168eff;
        }

        .register-btn {
            margin-top: 10px;
            background-color: #168eff;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .register-btn:hover {
            background-color: #3b3b3b;
        }

        .basic-link {
            margin-top: 15px;
            display: inline-block;
            font-size: 14px;
            color: #999;
            text-decoration: none;
        }

        .basic-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="register-container">
        <div class="left-panel">
            <h1>Join CrudApi</h1>
            <p>Create your account and get started</p>
            <i class="fas fa-user-plus fa-3x mt-4"></i>
        </div>
        <div class="right-panel">
            <h2>Create Account</h2>
            <form id="registerForm" action="" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <span class="icon"><i class="fa fa-user"></i></span>
                        <input type="text" name="firstname" id="firstname" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <span class="icon"><i class="fa fa-user"></i></span>
                        <input type="text" name="lastname" id="lastname" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <span class="icon"><i class="fa fa-building"></i></span>
                        <input type="text" name="departement" id="departement" placeholder="Departement/Office" required>
                    </div>
                    <div class="form-group">
                        <span class="icon"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" id="username" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <span class="icon"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone" id="phone" placeholder="Phone Number" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <span class="icon"><i class="fas fa-check-circle"></i></span>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    </div>
                </div>
                <button type="submit" class="register-btn">Register</button>
            </form>
        </div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/toast.js') }}"></script>


<script>
    AOS.init();
    $(document).ready(function() {
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            const data = {
                firstname: $('#firstname').val(),
                lastname: $('#lastname').val(),
                departement: $('#departement').val(),
                name: $('#username').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                password: $('#password').val(),
                password_confirmation: $('#confirm_password').val()
            };

            // Kirim data via AJAX POST
            $.ajax({
                url: 'http://10.21.1.125:8000/api/register',
                type: 'POST',
                contentType: 'application/json',
                headers : {
                    'Accept' :'application/json'
                },
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.status === true) {
                        window.parent.postMessage({
                            event: 'show-toast',
                            type: 'success',
                            message: 'Registrasi berhasil'
                        }, '*');
                        window.parent.postMessage({
                            event: 'true'
                        }, '*');
                    } else {
                        window.parent.postMessage({
                            event: 'show-toast',
                            type: 'error',
                            message: response.message || 'Terjadi Kesalahan Pada Saat Registrasi'
                        }, '*');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Registrasi gagal';
                    try {
                        const errJson = JSON.parse(xhr.responseText);

                        if (errJson.errors) {
                            for (const key in errJson.errors) {
                                errJson.errors[key].forEach((msg) => {
                                    window.parent.postMessage({
                                        event: 'show-toast',
                                        type: 'error',
                                        message: msg
                                    }, '*');
                                });
                            }
                            return;
                        }
                        errorMsg = errJson.message || errorMsg;
                    } catch (e) {
                        errorMsg += 'Tidak dapat membaca respon error';
                    }

                    window.parent.postMessage({
                        event: 'show-toast',
                        type: 'error',
                        message: errorMsg
                    }, '*');
                }
            });
        });
    });
</script>


</html>