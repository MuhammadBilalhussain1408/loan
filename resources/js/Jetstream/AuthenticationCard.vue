<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6  bg-white shadow-md overflow-hidden sm:rounded-lg p-4">
            <slot />
        </div>
    </div>
</template>
