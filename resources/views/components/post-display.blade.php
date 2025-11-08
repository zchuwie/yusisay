@props(['post'])

<div class="flex flex-col items-center justify-center gap-[10px]">
    <div x-data="{
        expanded: true,
        showButton: false,
        checkHeight() {
            this.$nextTick(() => {
                const el = this.$refs.content;
                if (el.scrollHeight > 134) {
                    this.showButton = true;
                }
            });
        }
    }" x-init="checkHeight"
        class="relative bg-[#fafafa] border border-[#dddddd] p-[20px] px-[37px] w-[700px] rounded-2xl flex flex-col items-center justify-center mb-2">
        
        <div class="w-full flex flex-col justify-center gap-[10px]">
            <div class="flex justify-between items-center w-full">
                <div class="flex items-center gap-3 flex-1">
                    <x-user-avatar :user="$post->user" :isAnonymous="$post->is_anonymous" />

                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900 text-sm">
                                {{ $post->is_anonymous ? 'Anonymous' : $post->user->name }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $post->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
                
                <x-post-menu :post="$post" />
            </div>

            <div x-ref="content" :class="expanded ? 'max-h-none' : 'max-h-[134px] overflow-hidden'"
                class="transition-all duration-300 ease-in-out mb-[10px] flex justify-start items-start text-[16px] text-[#454545] leading-[30px]">
                {{ $post->content }}
            </div>
        </div>

        <button x-show="showButton" @click="expanded = !expanded" x-transition
            class="absolute bottom-[10px] right-[10px] text-[#6a6a6a] text-[13px] hover:underline focus:outline-none">
            <span x-text="expanded ? 'Minimize' : 'Expand'"></span>
        </button>
    </div>
</div>