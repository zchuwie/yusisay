<div class="relative group">
    <a href="{{ route('posts.show', $postId) }}" class="absolute inset-0 z-10"></a>
    <div
        class="bg-[#fafafa] border border-[#dddddd] p-[30px] px-[37px] w-[700px] shadow rounded-2xl flex flex-col items-center justify-center mb-5 font-sans">
        <div class="flex flex-col justify-center gap-6 w-full">

            <!-- Post details (who + time + comments) -->
            <div class="flex justify-between items-center w-full">

                <!-- who & time -->
                <div class="flex items-center gap-2.5 w-full">
                    <div class="text-[16px] font-bold text-[#454545]">{{ $username }}</div>
                    <div class="mt-1 text-[12px] text-[#8d8d8d]">{{ $time }}</div>
                </div>

                <!-- comment + dot menu -->
                <div class="flex justify-end items-center gap-1.5 w-full self-center">
                    <div class="flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22"
                            fill="none">
                            <path
                                d="M1.99169 15.3417C2.13873 15.7126 2.17147 16.119 2.08569 16.5087L1.02069 19.7987C0.986375 19.9655 0.995248 20.1384 1.04647 20.3008C1.09769 20.4633 1.18955 20.61 1.31336 20.727C1.43716 20.844 1.5888 20.9274 1.75389 20.9693C1.91898 21.0113 2.09205 21.0104 2.25669 20.9667L5.66969 19.9687C6.03741 19.8958 6.41822 19.9276 6.76869 20.0607C8.90408 21.0579 11.3231 21.2689 13.5988 20.6564C15.8746 20.0439 17.861 18.6473 19.2074 16.7131C20.5538 14.7788 21.1738 12.4311 20.958 10.0842C20.7422 7.73738 19.7044 5.54216 18.0278 3.88589C16.3511 2.22962 14.1434 1.21873 11.7941 1.03159C9.44475 0.844449 7.10483 1.49308 5.18713 2.86303C3.26944 4.23299 1.89722 6.23624 1.31258 8.51933C0.727946 10.8024 0.96846 13.2186 1.99169 15.3417Z"
                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="text-[#454545] self-center text-[12px] mt-1">{{ $commentsCount }}</div>
                    <div class="ml-5 mt-[1px] self-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="4" height="18" viewBox="0 0 4 18"
                            fill="none">
                            <path
                                d="M2 10C2.55228 10 3 9.55228 3 9C3 8.44772 2.55228 8 2 8C1.44772 8 1 8.44772 1 9C1 9.55228 1.44772 10 2 10Z"
                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M2 3C2.55228 3 3 2.55228 3 2C3 1.44772 2.55228 1 2 1C1.44772 1 1 1.44772 1 2C1 2.55228 1.44772 3 2 3Z"
                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M2 17C2.55228 17 3 16.5523 3 16C3 15.4477 2.55228 15 2 15C1.44772 15 1 15.4477 1 16C1 16.5523 1.44772 17 2 17Z"
                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- post text -->
            <div class="mb-2.5 flex justify-start items-start text-[16px] text-[#454545] leading-[30px]">
                {{ $content }}
            </div>

        </div>
    </div>
</div>
