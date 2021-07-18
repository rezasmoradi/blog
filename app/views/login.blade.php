@extends('partials.header')

<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="border-1 border-danger">
        <form action="/login" method="post">
            <div class="form-label my-4">
                <label>ایمیل
                    <input class="form-control" type="text" name="email">
                </label>
            </div>
            <div class="form-label my-4">
                <label>رمز عبور
                    <input class="form-control" type="password" name="password">
                </label>
            </div>
            <div class="form-label my-5 d-grid">
                <input type="submit" value="ورود" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>

@extends('partials.footer')