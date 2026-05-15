<div id="cabanas-loader" class="fixed inset-0 z-[9999] flex items-center justify-center transition-opacity duration-500"
    style="background: rgba(255,255,255,0.92);">

    <div
        class="mx-auto max-w-[90%] sm:max-w-md md:max-w-lg lg:max-w-xl overflow-hidden drop-shadow-2xl bg-transparent font-[Inter]">
        <div class="flex p-6 sm:p-8 justify-center items-center min-h-[60vh] sm:h-[450px] bg-transparent">
            <div class="text-center space-y-6">
                <!-- Spinner -->
                <div
                    class="loader-spinner mx-auto h-16 w-16 animate-spin rounded-full border-4 border-gray-300 border-t-[#964B00] sm:h-20 sm:w-20 md:h-24 md:w-24">
                </div>

                <!-- Title -->
                <div class="text-[#964B00] font-medium text-2xl sm:text-3xl md:text-4xl opacity-90 animate-fadeIn">
                    Almost There...
                </div>

                <!-- Subtitle -->
                <div class="text-black text-sm sm:text-sm md:text-base opacity-80 animate-fadeIn space-y-1">
                    <p>We're getting everything ready for you...</p>
                    <p>Sit tight for just a moment.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    #cabanas-loader {
        overflow: hidden;
    }
    body.loader-active {
        overflow: hidden !important;
        height: 100vh !important;
        padding-right: 0 !important;
    }
</style>
