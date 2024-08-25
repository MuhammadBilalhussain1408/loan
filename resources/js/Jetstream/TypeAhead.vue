<template>
    <div class="relative">
        <textarea :id="id" ref="input" v-bind="$attrs"
                  class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none"
                  :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"/>
        <div
            class="z-10 absolute overflow-y-auto max-h-64  bg-white divide-y divide-gray-100 shadow w-full dark:bg-gray-700"
            v-if="open">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200 w-full">
                <li v-for="suggestion in matches">
                    <a href="#"  @click="suggestionClick(suggestion)" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{
                            suggestion
                        }}</a>
                </li>
            </ul>
        </div>

    </div>
</template>

<script>
import pickBy from "lodash/pickBy";

export default {
    inheritAttrs: false,
    props: {
        id: {
            type: String,
        },
        suggestions: {
            type: [Array, Object],
            default: []
        },
        suggestionsUrl: {
            type: String,
            default: ''
        },
        modelValue: String,
        label: String,
        error: String,

    },
    data() {
        return {
            matches: [],
            open: false
        }
    },
    methods: {
        focus() {
            this.$refs.input.focus()
        },
        select() {
            this.$refs.input.select()
        },
        suggestionClick(suggestion){
            alert(suggestion)
        }
    },
    watch: {
        modelValue: {
            handler: _.debounce(function (val) {
                if (this.suggestions.length) {

                }
                if (this.suggestionsUrl) {
                    axios.get(this.suggestionsUrl + "?s=" + val).then(response => {
                        this.matches = []
                        response.data.forEach(item => {
                            const result = item.symptoms.split(/\r?\n/)
                            result.forEach(res => {
                                if (res.includes(val)) {
                                    this.matches.push(res)
                                }
                            })

                        })
                        if (this.matches.length) {
                            this.open = true
                        }
                    })
                }
                console.log(val)
            }, 500),
        },
    },
    mounted() {
        console.log(this.suggestionsUrl)
    }
}
</script>
