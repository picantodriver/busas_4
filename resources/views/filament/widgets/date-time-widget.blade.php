<div class="w-full px-6 py-5 shadow-md rounded-lg" style="background: linear-gradient(90deg, #26a8d3, #00aedd, #00b6e4, #00cbf7);">
    <div class="flex justify-between items-center text-white font-medium">
        <div class="flex items-center space-x-2">
            <span style="font-size: 1.5rem; font-weight: font-medium;">
                Welcome, 
                @php
                    $nameParts = explode(' ', auth()->user()->name);
                    $firstName = strtoupper($nameParts[0]);
                    $lastName = strtoupper(end($nameParts));
                    $secondName = count($nameParts) > 2 ? ' ' . strtoupper($nameParts[1]) : '';
                    echo $firstName . $secondName . ' ' . $lastName;
                @endphp! 
                <span style="font-size: 2rem;">🎉</span>
            </span>
        </div>
        <!-- <div class="flex items-center space-x-2">
            <x-heroicon-o-calendar class="w-5 h-5 text-white" />
            
            <span id="currentDateTime">{{ $currentDateTime }}</span>
        </div> -->
    </div>

    <div class="mt-4 text-white font-medium text-left w-full flex">
        <div class="w-3/8">
            <span style="font-size: 0.95rem; font-weight: normal;"><strong>The Bicol University Student Archiving System (BUSAS)</strong> is a digital platform designed to efficiently store, manage, and retrieve academic records of all the Students of Bicol University. It serves as a repository for crucial graduate information, ensuring the safekeeping of official transcripts and other academic documents. The system is developed to streamline administrative processes related to record-keeping, certification issuance, and data retrieval, enhancing the efficiency and accuracy of document management within the university.</span>
        </div>
        <!-- Empty div to push Date/Time on the right side -->
        <div class="w-1/4"></div>
    </div>
</div>