@extends('frontdesk.layouts.app')

@section('content')

    <div class="p-4 sm:p-6 space-y-6">

        {{-- 1. Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white"> Booking & Refund Settings</h1>
        </div>

        {{-- Success/Error Message Container (Styled like the admin dashboard alerts) --}}
        @if(session('success'))
            <div class="p-4  text-sm bg-green-50 border border-green-200 text-green-800 dark:bg-green-900 dark:border-green-800 dark:text-green-200"
                role="alert">
                <span class="font-medium">Success:</span> {{ session('success') }}
            </div>
        @endif

        {{-- Settings Form Card --}}
        <div
            class="bg-white dark:bg-black p-4 sm:p-6  shadow-lg border dark:border-black max-w-4xl mx-auto">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 border-b pb-4 dark:border-black">
                Configure Parameters</h2>

            <form method="POST" action="{{ route('frontdesk.settings.update') }}" class="space-y-6">
                @csrf

                {{-- Deposit Percentage Field --}}
                <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4 md:gap-6">
                    <label for="deposit_percentage"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:col-span-1">
                        Booking Deposit Percentage
                    </label>
                    <div class="md:col-span-2">
                        <select name="deposit_percentage" id="deposit_percentage"
                            class="mt-1 block w-full border border-gray-300 dark:border-black  shadow-sm py-2 px-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-black focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="30" {{ $deposit == 30 ? 'selected' : '' }}>30%</option>
                            <option value="40" {{ $deposit == 40 ? 'selected' : '' }}>40%</option>
                            <option value="50" {{ $deposit == 50 ? 'selected' : '' }}>50%</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The percentage of the total amount due at
                            the time of booking confirmation.</p>
                    </div>
                </div>

                <hr class="dark:border-black">

                {{-- Refund Fee Percentage Field --}}
                <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4 md:gap-6">
                    <label for="refund_fee_percentage"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:col-span-1">
                        Refund Fee Percentage
                    </label>
                    <div class="md:col-span-2">
                        <select name="refund_fee_percentage" id="refund_fee_percentage"
                            class="mt-1 block w-full border border-gray-300 dark:border-black  shadow-sm py-2 px-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-black focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="0" {{ $refundFee == 0 ? 'selected' : '' }}>0%</option>
                            <option value="5" {{ $refundFee == 5 ? 'selected' : '' }}>5%</option>
                            <option value="10" {{ $refundFee == 10 ? 'selected' : '' }}>10%</option>
                            <option value="15" {{ $refundFee == 15 ? 'selected' : '' }}>15%</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The fee charged as a percentage of the
                            deposit amount in case of a refund.</p>
                    </div>
                </div>

                <hr class="dark:border-black">

                {{-- Include Refund Fee Checkbox --}}
                <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4 md:gap-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:col-span-1">
                        Refund Form Display
                    </label>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="include_refund_fee_in_form" value="1"
                                class="form-checkbox h-4 w-4 text-indigo-600 dark:text-indigo-500 border-gray-300 dark:border-black rounded dark:bg-black focus:ring-indigo-500"
                                {{ $includeRefundFeeInForm ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-200">Include refund fee percentage in
                                refund request form</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If checked, the refund fee will be visible
                            on the customer-facing refund request form.</p>
                    </div>
                </div>

                <hr class="dark:border-black">

                {{-- Save Button --}}
                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium  text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

    </div>

@endsection
