
              @if($room->amenities)
                @php
                  $icons = [
                    'Airconditioned' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6">
                                                                  <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M8 16a3 3 0 0 1-3 3m11-3a3 3 0 0 0 3 3m-7-3v4M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                                  <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M7 13v-3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3" />
                                                              </svg>',

                    'Minibar' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M7 3h10v18H7zM9 6h6M9 10h6M9 14h6M9 18h6" />
                                                              </svg>',

                    'Shower' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M5 14v-2q0-2.65 1.7-4.6T11 5.1V3h2v2.1q2.6.35 4.3 2.3T19 12v2z"/>
                                                                  <circle cx="8" cy="17" r="1"/><circle cx="12" cy="17" r="1"/><circle cx="16" cy="17" r="1"/>
                                                                  <circle cx="8" cy="20" r="1"/><circle cx="12" cy="20" r="1"/><circle cx="16" cy="20" r="1"/>
                                                              </svg>',

                    'Bath' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1m2 0V5a2 2 0 0 1 2-2h3v2.25M4 21l1-1.5M20 21l-1-1.5"/>
                                                              </svg>',

                    'Kitchen' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M19 3v12h-5c-.023-3.681.184-7.406 5-12m0 12v6h-1v-3M8 4v17M5 4v3a3 3 0 1 0 6 0V4"/>
                                                              </svg>',

                    'Balcony with sea view' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M4 13v8m4-8v8m8-8v8m-4-8v8m8-8v8M2 21h20M2 13h20m-4-3V3.6a.6.6 0 0 0-.6-.6H6.6a.6.6 0 0 0-.6.6V10" />
                                                              </svg>',

                    'Work Space' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M3 17h18M6 4h12v8H6zM4 21h16" />
                                                              </svg>',

                    'Hot & Cold Shower' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M5 14v-2q0-2.65 1.7-4.6T11 5.1V3h2v2.1q2.6.35 4.3 2.3T19 12v2z"/>
                                                                  <circle cx="8" cy="17" r="1"/><circle cx="12" cy="17" r="1"/><circle cx="16" cy="17" r="1"/>
                                                                  <circle cx="8" cy="20" r="1"/><circle cx="12" cy="20" r="1"/><circle cx="16" cy="20" r="1"/>
                                                              </svg>',

                    'Kitchen with stove for free use' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <rect x="3" y="4" width="18" height="16" rx="2"/>
                                                                  <path d="M8 8h8M8 12h8M8 16h8"/>
                                                              </svg>',

                    'Refrigerator' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="h-4 w-4 mr-1.5 inline-block align-middle"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  stroke="currentColor"
                                                                  stroke-width="1.6"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round">
                                                                  <path d="M7 3h10v18H7zM9 7h6M9 13h6"/>
                                                              </svg>',
                  ];
                @endphp

                <div class="flex flex-wrap gap-2 mt-2 font-[Inter]">
                  @foreach(explode(',', $room->amenities) as $amenity)
                    @php $name = trim($amenity); @endphp
                    <span
                      class="flex items-center gap-1 bg-white text-gray-800 text-xs  px-3 py-1.5  border border-gray-200 shadow-sm hover:bg-gray-100">
                      {!! $icons[$name] ?? '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 inline-block align-middle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9" /></svg>' !!}
                      {{ $name }}
                    </span>
                  @endforeach
                </div>
              @endif

