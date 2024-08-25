<template>
    <div
        class="relative flex items-top  bg-white  sm:items-center sm:pt-0">
        <div class="max-w-6xl pt-16 mx-auto sm:px-6 lg:px-8">
            <h4 class="font-bold text-2xl text-center">Please wait while we setup up your system. This shouldn't take
                more than a minute...</h4>
            <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">

                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 318 318" enable-background="new 0 0 318 318" xml:space="preserve">
                    <g id="XMLID_2_">
                        <g id="XMLID_7_">

                                <path id="XMLID_16_" fill="none" stroke="#39B54A" stroke-width="8"
                                      stroke-linecap="round"
                                      stroke-miterlimit="10" d="
                                M314,171.3v-24.5c0-4-3.2-7.3-7.3-7.3H266c-3.3,0-6.2-2.2-7-5.4c-2.5-10-6.5-19.5-11.6-28.1c-1.7-2.8-1.2-6.5,1.1-8.8l28.8-28.8
                                c2.8-2.8,2.8-7.4,0-10.3l-17.3-17.3c-2.8-2.8-7.4-2.8-10.3,0l-28.8,28.8c-2.3,2.3-6,2.8-8.8,1.1c-8.6-5.2-18.1-9.2-28.1-11.6
                                c-3.2-0.8-5.4-3.7-5.4-7V11.3c0-4-3.2-7.3-7.3-7.3h-24.5c-4,0-7.3,3.2-7.3,7.3V52c0,3.3-2.2,6.2-5.4,7c-10,2.5-19.5,6.5-28.1,11.6
                                c-2.8,1.7-6.5,1.2-8.8-1.1L68.3,40.7c-2.8-2.8-7.4-2.8-10.3,0L40.7,58.1c-2.8,2.8-2.8,7.4,0,10.3l28.8,28.8c2.3,2.3,2.8,6,1.1,8.8
                                c-5.2,8.6-9.2,18.1-11.6,28.1c-0.8,3.2-3.7,5.4-7,5.4H11.3c-4,0-7.3,3.2-7.3,7.3v24.5c0,4,3.2,7.3,7.3,7.3H52c3.3,0,6.2,2.2,7,5.4
                                c2.5,10,6.5,19.5,11.6,28.1c1.7,2.8,1.2,6.5-1.1,8.8l-28.8,28.8c-2.8,2.8-2.8,7.4,0,10.3l17.3,17.3c2.8,2.8,7.4,2.8,10.3,0
                                l28.8-28.8c2.3-2.3,6-2.8,8.8-1.1c8.6,5.2,18.1,9.2,28.1,11.6c3.2,0.8,5.4,3.7,5.4,7v40.8c0,4,3.2,7.3,7.3,7.3h24.5
                                c4,0,7.3-3.2,7.3-7.3V266c0-3.3,2.2-6.2,5.4-7c10-2.5,19.5-6.5,28.1-11.6c2.8-1.7,6.5-1.2,8.8,1.1l28.8,28.8
                                c2.8,2.8,7.4,2.8,10.3,0l17.3-17.3c2.8-2.8,2.8-7.4,0-10.3l-28.8-28.8c-2.3-2.3-2.8-6-1.1-8.8c5.2-8.6,9.2-18.1,11.6-28.1
                                c0.8-3.2,3.7-5.4,7-5.4h40.8C310.8,178.5,314,175.3,314,171.3z"/>
                        </g>
                        <g id="XMLID_1_">

                                <circle id="XMLID_14_" fill="none"
                                        stroke-linecap="round"
                                        stroke="#39B54A" stroke-width="8" stroke-miterlimit="10" cx="159" cy="159"
                                        r="57"/>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="flex justify-center mt-4">
                <button @click="refresh"
                        class="items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none">
                    Refresh
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
svg {
    height: 200px;
    width: 200px;
    margin-left: calc(50% - 100px);
    animation: rotate 2s infinite 12s linear;
}

svg path {
    animation: stock 10s linear;
    animation-fill-mode: forwards;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes stock {
    from {
        stroke-dasharray: 10, 1452;
    }
    to {
        stroke-dasharray: 1450, 1452;
    }
}

circle {
    stroke-dasharray: 0, 1452;
    animation: stock 10s ease 10s;
    animation-fill-mode: forwards;
}
</style>

<script>
export default {
    props: {
        url: String,
    },
    methods: {

        refresh() {
            window.location = this.url
        },

    },
    mounted() {
        setTimeout(function () {
            window.location.reload();
        }, 10000);
    }
}
</script>
