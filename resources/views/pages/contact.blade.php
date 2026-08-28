<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page?->title() ?? 'Contact par pays' }}</title>
</head>
<body>
    <main>
        <h1>{{ $page?->title() ?? 'Contact par pays' }}</h1>

        @if (session('success'))
            <p role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div role="alert">
                <p>Please correct the following fields:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store', ['locale' => $locale]) }}">
            @csrf

            <label for="name">Name *</label>
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="255">

            <label for="email">Email *</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255">

            <label for="phone">Phone</label>
            <input id="phone" name="phone" value="{{ old('phone') }}" maxlength="50">

            <label for="company">Company</label>
            <input id="company" name="company" value="{{ old('company') }}" maxlength="255">

            <label for="country">Country</label>
            <input id="country" name="country" value="{{ old('country') }}" maxlength="100">

            <label for="message">Message *</label>
            <textarea id="message" name="message" required maxlength="10000">{{ old('message') }}</textarea>

            <button type="submit">Send message</button>
        </form>
    </main>
</body>
</html>
