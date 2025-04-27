<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Client Portal - Invoice System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
      href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
      rel="stylesheet"
    />

    <!-- Tailwind CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
      rel="stylesheet"
    />
  </head>

  <body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen flex flex-col items-center justify-center px-4 lg:px-8">
    <!-- Navbar -->
    <header class="w-full max-w-6xl py-4 flex justify-between items-center">
      <div class="flex items-center">
        <span class="font-bold text-xl text-blue-700 dark:text-blue-400">ClientPortal</span>
      </div>
      
      @if (Route::has('login'))
<nav class="space-x-4">
    @auth
    <a
      href="{{ url('/dashboard') }}"
      class="text-sm px-4 py-2 border rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition"
    >
      Dashboard
    </a>

    <!-- Hidden form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Logout link -->
    <a
      href="#"
      onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
      class="text-sm px-4 py-2 border rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition"
    >
      Log out
    </a>

    @else
    <a
      href="{{ route('login') }}"
      class="text-sm px-4 py-2 border rounded-md bg-blue-600 text-white hover:bg-blue-700 transition"
    >
      Client Login
    </a>
    @endauth
</nav>
@endif

    </header>

    <!-- Main -->
    <main class="w-full max-w-6xl mt-6 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
      <!-- Left Column -->
      <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md">
        <div class="mb-4 inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
          Exclusive Access
        </div>
        <h1 class="text-4xl font-bold text-blue-700 dark:text-blue-400 mb-4">Your Custom Invoice System</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Welcome to your dedicated invoice generation platform, built specifically for your business needs.
        </p>

        <div class="mb-8">
          <h2 class="text-xl font-semibold mb-3">Custom Features</h2>
          <ul class="space-y-2">
            <li class="flex items-center gap-2">
              <span class="text-green-500">✔</span> Branded invoice templates
            </li>
            <li class="flex items-center gap-2">
              <span class="text-green-500">✔</span> Secure data storage
            </li>
            <li class="flex items-center gap-2">
              <span class="text-green-500">✔</span> Integration with your systems
            </li>
            <li class="flex items-center gap-2">
              <span class="text-green-500">✔</span> Dedicated support channel
            </li>
          </ul>
        </div>

        <div class="flex gap-4">
          <a
            href="{{ route('login') }}"
            class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-md transition"
          >
            Access Your System
          </a>
          <a
            href="#contact"
            class="border border-gray-300 dark:border-gray-600 hover:border-blue-600 px-5 py-2 rounded-md transition"
          >
            Contact Support
          </a>
        </div>
      </div>

      <!-- Right Column -->
      <div class="bg-gradient-to-br from-blue-700 to-blue-900 dark:from-blue-800 dark:to-blue-950 text-white p-8 rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold">Client Portal</h2>
          <div class="bg-white/20 px-3 py-1 rounded text-sm">Authorized Access Only</div>
        </div>
        
        <div class="space-y-4 mb-6">
          <div class="bg-white/10 p-4 rounded-lg">
            <h3 class="font-medium mb-2">Secure Environment</h3>
            <p class="text-sm text-blue-100">Your data is protected with enterprise-grade encryption and access controls.</p>
          </div>
          
          <div class="bg-white/10 p-4 rounded-lg">
            <h3 class="font-medium mb-2">Tailored Workflow</h3>
            <p class="text-sm text-blue-100">Custom-built to match your specific business processes and requirements.</p>
          </div>
        </div>
        
        <div class="text-sm bg-blue-600/50 p-3 rounded-lg">
          Need assistance? Contact your dedicated account manager at support@clientportal.com
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="mt-12 text-center text-gray-500 dark:text-gray-400 text-sm">
      <div class="mb-2">Authorized access only. All activities are monitored and logged.</div>
      <div>&copy; 2025 Client Portal. Custom solution for [Client Name].</div>
    </footer>

    <!-- Dark mode script -->
    <script>
      if (
        window.matchMedia &&
        window.matchMedia('(prefers-color-scheme: dark)').matches
      ) {
        document.documentElement.classList.add('dark');
      }
    </script>
  </body>
</html>