<?php

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certification</title>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }

        body {
            font-family: Times New Roman, serif;
            width: 8.5in;
            height: 11in;
            position: relative;
            margin: 0;
            padding: 0;
        }

        .certificate {
            width: 6.5in;
            margin: 0 1in;
            /* padding-top: 2in; */
        }

        .title {
            padding-top: 4.7cm;
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .date {
            text-align: right;
            font-weight: bold;
            margin-bottom: 20px;

        }

        .concern {
            font-weight: bold;
            font-size: 11.5pt;
            margin-bottom: 20px;
        }

        .content {
            text-align: justify;
            line-height: 1.2;
            margin-bottom: 10px;
            text-indent: 50px;
            font-size: 11pt;
        }

        .content-issued {
            text-align: justify;
            line-height: 1.2;
            margin-bottom: 40px;
            text-indent: 50px;
            font-size: 11pt;
        }


        .signature {
            margin-top: 5px;
            margin-right: 0;
            font-size: 12pt;
            line-height: 15px;
        }

        .registrar-name {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12pt;
        }

        .registrar-title {
            margin-top: 0;
        }

        .footer {
            /* position: absolute; */
            /* padding-top: 3in; */
            /* bottom: 50px; */
            left: 1.25in;
            font-size: 9pt;
        }

        hr {
            margin-top: 3rem;
            border: none;
            border-top: 1px solid black;
            width: 100%;
        }

        .date-line {
            margin-top: 1.5rem;
            border: none;
            border-top: 0.5px solid black;
            width: 30%;
            margin-left: auto;
            margin-right: 0;
        }

        .sign-line {
            margin-top: 1.5rem;
            border: none;
            border-top: 0.5px solid black;
            width: 50%;
            margin-right: auto;
            margin-left: 0;
        }

        .or-details {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
            width: 100%;
            font-size: 11pt;
            margin-top: 10px;
        }

        .or-details span {
            flex: 1;
            text-align: center;
        }

        .or-details span:first-child {
            text-align: left;
            /* OR No. aligned to the left */
        }

        .or-details span:last-child {
            text-align: right;
            /* Date aligned to the right */
        }

        .receipt {
            text-decoration: underline;
            padding-right: 3rem;
        }

        .registrar {
            font-size: 11pt;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <div class="title">HONORABLE DISMISSAL</div>
        <div class="date">{{ now()->format('F d, Y') }}</div>
        <div class="concern">TO WHOM IT MAY CONCERN:</div>

        <div class="content">
            This is to certify that <strong>
                {{ $student->sex == 'M' ? 'Mr.' : 'Ms.' }}
                {{ strtoupper($student->first_name) }} {{ strtoupper($student->middle_name ?? '') }} {{ strtoupper($student->last_name) }} {{ strtoupper($student->suffix ?? '') }}</strong>
            @php
            $record = $student->records()->first();
            $campus = 'N/A';
            $collegeName = '';

            if ($record && $record->college) {
            $campusName = $record->campus->campus_name ?? '';

            if (in_array($campusName, ['Main Campus', 'East Campus', 'Daraga Campus'])) {
            $collegeName = 'Bicol University ' . $record->college->college_name;
            switch ($campusName) {
            case 'Main Campus':
            $campus = 'Main Campus, Legazpi City';
            break;
            case 'East Campus':
            $campus = 'East Campus, Legazpi City';
            break;
            case 'Daraga Campus':
            $campus = 'Daraga Campus, Daraga, Albay';
            break;
            }
            } else {
            $collegeName = $record->college->college_name;
            switch ($record->college->college_name) {
            case 'Bicol University Gubat':
            $campus = 'Gubat, Sorsogon';
            break;
            case 'Bicol University Guinobatan':
            $campus = 'Guinobatan, Albay';
            break;
            case 'Bicol University Polangui':
            $campus = 'Polangui, Albay';
            break;
            case 'Bicol University Tabaco':
            $campus = 'Tabaco City, Albay';
            break;
            }
            }
            }
            @endphp
            who graduated with/took up subjects towards the degree of <strong>{{ $student->records()->first()?->curricula?->programs?->program_name }} ({{ $student->records()->first()?->curricula?->programs?->program_abbreviation }}),
            </strong>from {{ $collegeName }}, {{ $campus }} is hereby granted honorable dismissal effective this date.
        </div>
        <div class="content">
            {{ $student->sex == 'M' ? 'His' : 'Her' }} Official Transcript of Record will be forwarded upon request by sending the lower portion of this honorable dismissal to the University/college.
        </div>

        <div class="signature">
            @php
            $certifiedBy = App\Models\Signatories::where('employee_designation', 'University Registrar')
            ->first();
            @endphp
            <div style="text-align: center; width: 215px; float: right;">
                <div class="registrar-name">{{ strtoupper($certifiedBy->employee_name) }}{{ $certifiedBy->suffix ? ', ' . $certifiedBy->suffix : '' }}</div>
                <div class="registrar-title">{{ $certifiedBy->employee_designation }}</div>
            </div>
        </div>
        @php
        $record = $student->records()->first();
        $location = 'N/A';
        $collegeName = '';

        if ($record && $record->college) {
        $campusName = $record->campus->campus_name ?? '';

        if (in_array($campusName, ['Main Campus', 'East Campus', 'Daraga Campus'])) {
        $collegeName = 'BU ' . $record->college->college_name;
        switch ($campusName) {
        case 'Main Campus':
        case 'East Campus':
        $location = 'Legazpi City';
        break;
        case 'Daraga Campus':
        $location = 'Daraga, Albay';
        break;
        }
        } else {
        $collegeName = $record->college->college_name;
        switch ($record->college->college_name) {
        case 'Bicol University Gubat':
        $location = 'Gubat, Sorsogon';
        break;
        case 'Bicol University Guinobatan':
        $location = 'Guinobatan, Albay';
        break;
        case 'Bicol University Polangui':
        $location = 'Polangui, Albay';
        break;
        case 'Bicol University Tabaco':
        $location = 'Tabaco City, Albay';
        break;
        }
        }
        }
        @endphp
        <hr>
        <hr style="margin-top: 1rem; width: 50%;">
        <hr style="margin-top: 1rem; width: 50%;">
        <hr style="margin-top: 1rem; width: 50%;">
        <div class="registrar" style="text-align: center; line-height: 50%; padding-bottom: 0.5rem;">(Complete Name of School and Address)</div>
        <hr class="date-line">
        <div class="registrar" style="padding-left: 520px; line-height: 50%;">Date</div>
        <div class="registrar" style="font-weight: bold;">The Registrar</div>
        <div class="registrar">{{ $collegeName }}</div>
        <div class="registrar">{{ $location }}</div><br>
        <div class="registrar">Sir/Madam:</div><br>
        <div class="content">
            <strong>
                {{ $student->sex == 'M' ? 'Mr.' : 'Ms.' }}
                {{ strtoupper($student->first_name) }} {{ strtoupper($student->middle_name ?? '') }} {{ strtoupper($student->last_name) }} {{ strtoupper($student->suffix ?? '') }},</strong>
            @php
            $record = $student->records()->first();
            $campus = 'N/A';
            $collegeName = '';

            if ($record && $record->college) {
            $campusName = $record->campus->campus_name ?? '';

            if (in_array($campusName, ['Main Campus', 'East Campus', 'Daraga Campus'])) {
            $collegeName = 'Bicol University ' . $record->college->college_name;
            switch ($campusName) {
            case 'Main Campus':
            $campus = 'Main Campus, Legazpi City';
            break;
            case 'East Campus':
            $campus = 'East Campus, Legazpi City';
            break;
            case 'Daraga Campus':
            $campus = 'Daraga Campus, Daraga, Albay';
            break;
            }
            } else {
            $collegeName = $record->college->college_name;
            switch ($record->college->college_name) {
            case 'Bicol University Gubat':
            $campus = 'Gubat, Sorsogon';
            break;
            case 'Bicol University Guinobatan':
            $campus = 'Guinobatan, Albay';
            break;
            case 'Bicol University Polangui':
            $campus = 'Polangui, Albay';
            break;
            case 'Bicol University Tabaco':
            $campus = 'Tabaco City, Albay';
            break;
            }
            }
            }
            @endphp
            who graduated with/took up subjects towards the degree of <strong>{{ $student->records()->first()?->curricula?->programs?->program_name }} ({{ $student->records()->first()?->curricula?->programs?->program_abbreviation }}),
            </strong>from {{ $collegeName }}, {{ $campus }} is hereby granted honorable dismissal effective this date is temporarily enrolled in the _____________________________pending receipt of {{ $student->sex == 'M' ? 'his' : 'her' }} Official Transcript of Record.
        </div>
        <div class="content">
            In connection with this, may I request that {{ $student->sex == 'M' ? 'his' : 'her' }} Official Transcript of Record be sent to this University/college immediately.
        </div>
        <hr class="date-line" style="border-top: 1px solid black;">
        <div class="registrar" style="padding-left: 480px; line-height: 50%;">Requesting Officer</div>
        <hr class="date-line" style="border-top: 1px solid black;">
        <div class="registrar" style="font-style: italic; font-weight: bold; line-height: 50%;">Not valid unless the student's signature is affixed below. <span style="padding-left: 150px; padding-top: 5px; font-weight: normal; font-style: normal;">Designation</span></div>
        <hr class="sign-line" style="border-top: 1px solid black;">
        <div class="signature" style="margin-left:3rem;  font-size: 10pt; font-style: italic; line-height: 50%;">(Signature over printed name of student)</div><br>

        <div class="or-details">
            @php
                $receipt = App\Models\Receipt::orderBy('date', 'desc')->orderBy('id', 'desc')->first();
            @endphp
            <span>OR No: <span class="receipt">{{ $receipt->or_number }}</span></span>
            <span>Amount: <span class="receipt">{{ $receipt->amount }}</span></span>
            <span>Date: <span class="receipt">{{ $receipt->date_of_or }}</span></span>
        </div>
        <div class="date_of_grad">Last Term Enrolled: <span class="receipt">2nd Sem. SY 2009-2010</span></div><br>


        <div class="footer">
            <div class="footer_left" style="float: left; text-align: left;">
                <strong>BU-F-UREG-08</strong><br>
                <strong>Effectivity Date: Mar. 9, 2011</strong><br>
            </div>
            <div class="footer_right" style="float: right; text-align: right;">
                {{-- @php
            $initials = App\Models\User::find(auth()->id())->initials ?? '';
            @endphp --}}
                <span><strong>Revision: 1</strong><br></span>
                {{-- <span>/{{ $initials }}</span> --}}
            </div>
        </div>
    </div>
</body>

</html>