<script setup>
import {Head, Link} from '@inertiajs/vue3';
import ApplicationMark from "@/Components/ApplicationMark.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome"/>

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen selection:text-white">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10 text-lg">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                  class="font-semibold text-gray-200 hover:text-gray-400 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-purple-500">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')"
                      class="font-semibold text-gray-200 hover:text-gray-400 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-purple-500">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')"
                      class="ml-4 font-semibold text-gray-200 hover:text-gray-400 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-purple-500">
                    Register
                </Link>
            </template>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="flex justify-center">
                <ApplicationMark/>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div
                        class="bg-background-light-dark border-double relative rounded-lg p-6 flex focus:outline focus:outline-2 focus:outline-purple-500">
                        <div class="border-triple"></div>
                        <div>
                            <div class="h-16 w-16 bg-blue-800/20 flex items-center justify-center rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-7 h-7 stroke-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-white">Know which desk is available</h2>

                            <p class="mt-4 text-gray-400 text-sm leading-relaxed">
                                No more searching for an empty desk in multiple buildings. With DeskPlan you can see
                                which desks are available in your office.
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-background-light-dark border-double relative rounded-lg p-6 flex focus:outline focus:outline-2 focus:outline-purple-500">
                        <div class="border-triple"></div>
                        <div>
                            <div class="h-16 w-16 bg-blue-800/20 flex items-center justify-center rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-7 h-7 stroke-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>

                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-white">Let your colleagues know where you
                                are</h2>

                            <p class="mt-4 text-gray-400 text-sm leading-relaxed">
                                With DeskPlan you can let your colleagues know where you are working today. Even if you
                                are working from home.
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-background-light-dark border-double relative rounded-lg p-6 flex focus:outline focus:outline-2 focus:outline-purple-500">
                        <div class="border-triple"></div>
                        <div>
                            <div class="h-16 w-16 bg-blue-800/20 flex items-center justify-center rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-7 h-7 stroke-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>

                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-white">Make a reservation for your table lighting fast</h2>

                            <p class="mt-4 text-gray-400 text-sm leading-relaxed">
                                With DeskPlan you can make a reservation for your desk in a few clicks. No more
                                searching for a desk in multiple buildings
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
