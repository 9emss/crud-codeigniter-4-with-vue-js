<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import '../../api'

// init router
const router = useRouter()

const token = localStorage.getItem('token')

// init route
const route = useRoute()

// define state
const title = ref("")
const description = ref("")
const errors = ref([])

const isFetching = ref(true)

// onMounted
async function getPostData() {
    isFetching.value = true

    // fetch detail data post by post ID
    await axios.get(`api/post/${route.params.id}`, {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        Credentials: 'include'
    })
        .then(response => {
            title.value = response.data.data.title
            description.value = response.data.data.description
            // console.log(response.data)
        }).catch(err => {
            // console.log(err)
            errors.value = "Error, while fetching!"
        }).finally(() => {
            isFetching.value = false
        })


}

onMounted(() => getPostData())

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
// method update post
async function updatePost() {


    // init formData
    let formData = new FormData()

    // assign state value to formData
    formData.append('title', title.value)
    formData.append('description', description.value)
    // formData.append('_method', 'PUT')

    await axios.put(`api/post/${route.params.id}`, formData, {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        Credentials: 'include'
    })
        .then(() => {
            router.push({
                path: '/post'
            })
        })
        .catch((error) => {

            // assign error value to state errors
            errors.value = error.response.data
        })
}

</script>

<template>
    <div class="container mb-5 mt-5 px-10 py-10">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 rounded shadow">
                    <div class="card-body">

                        <form @submit.prevent="updatePost()" class="px-10 py-10">
                            <div class="mb-6">
                                <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                                    title</label>
                                <input type="text" id="title"
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"
                                    v-model="title" placeholder="Title Post..." required>

                                <div v-if="errors.title"
                                    class="flex p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400"
                                    role="alert">
                                    <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Info</span>
                                    <div>
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span
                                                class="font-medium">Oops!</span> {{ errors.title[0] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:col-span-2 mb-6">
                                <label for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea id="description" rows="8"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    v-model="description" placeholder="Your description here"></textarea>

                                <div v-if="errors.description"
                                    class="flex p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400"
                                    role="alert">
                                    <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Info</span>
                                    <div>
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span
                                                class="font-medium">Oops!</span> {{ errors.description[0] }}</p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>