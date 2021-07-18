<nav style="margin-top: 1rem;" class="container">
    <ul class="dropdown justify-content-around">
        <li>
            <a href="/" class="btn">خانه</a>
        </li>
        @if($user = json_decode(\Core\Session::get('user')))
            <li>
                <div class="btn">{{$user->name}}</div>
                <ul class="dropdown">
                    <li style="<?= $_SERVER['QUERY_STRING'] === 'profile' ? 'background-color: #eeeeee;' : ''; ?>"
                        class="d-flex justify-content-between align-items-center">
                        <a href="/profile" class="text-decoration-none">پروفایل</a>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d6efd"
                             class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd"
                                  d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </li>
                    <li style="<?= $_SERVER['QUERY_STRING'] === 'posts' ? 'background-color: #eeeeee;' : ''; ?>"
                        class="d-flex justify-content-between align-items-center">
                        <a href="/posts" class="text-decoration-none d-grid">پست&zwnj;ها</a>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d6efd"
                             class="bi bi-files" viewBox="0 0 16 16">
                            <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"/>
                        </svg>

                    </li>
                    <li style="<?= $_SERVER['QUERY_STRING'] === 'categories' ? 'background-color: #eeeeee;' : ''; ?>"
                        class="d-flex justify-content-between align-items-center">
                        <a href="/categories" class="text-decoration-none d-grid">دسته&zwnj;بندی&zwnj;ها</a>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d6efd"
                             class="bi bi-folder" viewBox="0 0 16 16">
                            <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/>
                        </svg>
                    </li>
                    <li style="<?= $_SERVER['QUERY_STRING'] === 'users' ? 'background-color: #eeeeee;' : ''; ?>"
                        class="d-flex justify-content-between align-items-center">
                        <a href="/users" class="text-decoration-none d-grid">کاربران</a>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d6efd"
                             class="bi bi-people" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                        </svg>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <a href="/logout" class="text-decoration-none d-grid">خروج</a>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#dc3545"
                             class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
                            <path fill-rule="evenodd"
                                  d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
                        </svg>
                    </li>
                </ul>
            </li>
        @else
            <li>
                <a href="/login" class="btn">ورود</a>
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-grip-vertical" viewBox="0 0 16 16">
                        <path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                    </svg>
                </a>
                <a href="/login" class="btn">ثبت&zwnj;نام</a>
            </li>
        @endif
        <li>
            <div class="btn">آموزش&zwnj;ها</div>
            <!-- First level sub dropdown -->
            <ul>
                <li>
                    <div class="d-flex justify-content-between align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-chevron-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        PHP
                    </div>
                    <!-- Second level sub dropdown -->
                    <ul style="background-color: #ce51c2;">
                        <li>
                            <a>Basic</a>
                        </li>
                        <li>
                            <a>OOP</a>
                        </li>
                        <li>
                            <a>MVC</a>
                        </li>
                        <li>
                            <a>Laravel</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="d-flex justify-content-between align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-chevron-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        JS
                    </div>
                    <!-- Second level sub dropdown -->
                    <ul style="background-color: #e2c741;">
                        <li>
                            <a>Basic</a>
                        </li>
                        <li>
                            <a>ES</a>
                        </li>
                        <li>
                            <a>JQuery</a>
                        </li>
                        <li>
                            <a>React</a>
                        </li>
                        <li>
                            <a>NodeJs</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="d-flex justify-content-between align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-chevron-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        Database
                    </div>
                    <!-- Second level sub dropdown -->
                    <ul style="background-color: #e27741;">
                        <li>
                            <div class="d-flex justify-content-between align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-chevron-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                                SQL
                            </div>
                            <!-- Second level sub dropdown -->
                            <ul style="background-color: #e27741;">
                                <li>
                                    <a>MySQL</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="d-flex justify-content-between align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-chevron-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                                NoSQL
                            </div>
                            <!-- Second level sub dropdown -->
                            <ul style="background-color: #e27741;">
                                <li>
                                    <a>MongoDB</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</nav>