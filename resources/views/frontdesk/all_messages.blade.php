@extends('frontdesk.layouts.app')

@section('content')

    <div class="p-4 sm:p-6 space-y-6">

        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
            Customer Contact Messages
        </h1>

        <div class="overflow-x-auto  shadow-md border border-gray-200 dark:border-black">
            <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">

                <thead class="bg-gray-50 dark:bg-black">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                            Name</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                            Email</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                            Phone</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                            Message Snippet</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                            Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">
                    @foreach ($contacts as $contact)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition duration-150">

                            {{-- Name (Changed to text-xs and font-normal) --}}
                            <td class="px-6 py-4 text-xs font-normal text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $contact->name }}
                            </td>

                            {{-- Email (Changed to text-xs) --}}
                            <td
                                class="px-6 py-4 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 whitespace-nowrap">
                                <a href="mailto:{{ $contact->email }}" class="hover:underline">{{ $contact->email }}</a>
                            </td>

                            {{-- Phone (Changed to text-xs and ) --}}
                            <td class="px-6 py-4 text-xs  text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ $contact->phone ?? 'N/A' }}
                            </td>

                            {{-- Message Snippet (Changed to text-xs and ) --}}
                            <td class="px-6 py-4 text-xs  text-gray-700 dark:text-gray-300 max-w-sm">
                                {{ Str::limit($contact->message, 80) }}
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-2">

                                <a href="{{ url('send_email', $contact->id) }}"
                                    class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z" />
                                    </svg>
                                </a>

                                <form action="{{ url('delete_message', $contact->id) }}" method="GET" class="inline-block ml-1"
                                    onsubmit="return confirm('Delete this message?');">
                                    <button type="submit"
                                        class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

