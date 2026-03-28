<div>
    <div class="flex h-screen bg-gray-50">
        <!-- Left Sidebar Menu -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-800">Menu</h1>
            </div>

            <nav class="p-2">
                <ul class="space-y-1">
                    @foreach($menuItems as $key => $item)
                        <li>
                            <button wire:click="setActiveMenu('{{ $key }}')" class="w-full flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 
                                               {{ $activeMenu === $key ? 'bg-blue-50 text-blue-600' : '' }}">
                                <!-- Heroicon SVG -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $item['icon'] }}" />
                                </svg>
                                <span class="ml-3">{{ $item['label'] }}</span>

                                @if(isset($item['submenu']))
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-auto h-4 w-4 transform transition-transform duration-200 
                                                            {{ $activeMenu === $key ? 'rotate-90' : '' }}" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                            </button>

                            <!-- Submenu items -->
                            @if(isset($item['submenu']) && $activeMenu === $key)
                                <ul class="ml-8 mt-1 space-y-1">
                                    @foreach($item['submenu'] as $subKey => $subItem)
                                        <li>
                                            <button wire:click="setActiveMenu('{{ $key }}', '{{ $subKey }}')"
                                                class="w-full flex items-center p-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 
                                                                                       {{ $activeSubMenu === $subKey ? 'bg-blue-50 text-blue-600' : '' }}">
                                                <span>{{ $subItem }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        <!-- Right Content Area -->
        <div class="flex-1 overflow-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    @if($activeSubMenu)
                        {{ $menuItems[$activeMenu]['submenu'][$activeSubMenu] }}
                    @else
                        {{ $menuItems[$activeMenu]['label'] }}
                    @endif
                </h2>

                <div class="text-gray-600">
                    @if($activeMenu === 'dashboard')
                        <p>Welcome to your dashboard. Here you can see an overview of your application.</p>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <a href="{{ route('marks-entry2') }}" target="_blank" 
                               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Go to Marks Entry
                            </a>

                            <a href="{{ route('studentcr') }}" target="_blank" 
                               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Go to Student CR
                            </a>

                            <a href="{{ route('marks-register') }}" target="_blank" 
                               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2, focus:ring-blue-500 transition-colors">
                                Go to Marks Register
                            </a>

                            <a href="{{ route('marks-register2') }}" target="_blank" 
                               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Go to Marks Register 2
                            </a>
                        </div>
                    @elseif($activeMenu === 'basic')
                        <p>Basic management section. Select a submenu option.</p>
                    @elseif($activeMenu === 'exam')
                        @if($activeSubMenu === 'examdashboard')
                            <p>Exam dashboard...</p>
                            {{-- @livewire('ex24-detail-comp') --}}
                            @livewire('ex24-detail2-comp')
                            <div class="mt-4">
                                @livewire('ex10-studentdb-comp', key('ex10-studentdb-main'))
                                @livewire('bs07-subject-comp', key('bs07-subject-main'))
                                @livewire('ex25-setting-comp', key('ex25-setting-main'))
                            </div>
                        @elseif($activeSubMenu === 'examname')
                            <p>Exam names management...</p>
                            @livewire('ex20-name-comp')
                        @elseif($activeSubMenu === 'examtype')
                            <p>Exam types management...</p>
                            @livewire('ex21-type-comp')
                        @elseif($activeSubMenu === 'exampart')
                            <p>Exam parts management...</p>
                            @livewire('ex22-part-comp')
                        @elseif($activeSubMenu === 'exammode')
                            <p>Exam modes management...</p>
                            @livewire('ex23-mode-comp')
                        @else
                            <p>Exam management section. Select a submenu option.</p>
                        @endif
                    @elseif($activeMenu === 'schools')
                        <p>Schools management section. Select a submenu option.</p>
                    @elseif($activeMenu === 'sessions')
                        <p>Sessions management section. Select a submenu option.</p>
                    @elseif($activeMenu === 'orders')
                        <p>Order management section. View and process customer orders.</p>
                    @elseif($activeMenu === 'customers')
                        <p>Customer management section. View and edit customer information.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>