<script setup>
import { ref, onMounted } from 'vue';

import axios from 'axios';
import { RouterLink } from 'vue-router';

const posts = ref([])

const fetchedData = ref({})
const isFetching = ref(true)


async function fetchDataPost() {

    isFetching.value = true

    await axios.get('api/post').then(response => {
        posts.value = response.data.data
        console.log(posts.value)
    }).catch(err => {
        posts.value = "Error, while fetching!"
    }).finally(() => {
        isFetching.value = false
    })

}

onMounted(() => fetchDataPost())

// method delete post
const deletePost = async (id) => {

    // delete post with API
    await axios.delete(`/api/post/${id}`)
        .then(() => {

            // call method fetchDataPost
            fetchDataPost()
        })

}
</script>

<template>
    <div class="container mt-5 mb-5 ml-2 px-10 py-10">
        <div class="row">
            <div class="col-md-12">
                <RouterLink to="/create"
                    class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-4 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 mb-2">
                    Add New Post</RouterLink>
                <div class="care border-0 rounded shadow mt-4">
                    <div class="card-body">

                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 text-center">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Title
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Description
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="posts.length == 0"
                                        class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                        <td colspan="4" class="px-6 py-4">
                                            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                                                role="alert">
                                                <span class="font-medium">Danger alert!</span> Posts Not Available!!
                                            </div>
                                        </td>

                                    </tr>

                                    <tr v-else v-for="(post, index) in posts" :key="index"
                                        class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ index + 1 }}
                                        </th>
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ post.title }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ post.description }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                                <RouterLink :to="{ name: 'posts.edit', params: { id: post.id } }"
                                                    class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:focus:ring-yellow-900">
                                                    Edit
                                                </RouterLink>
                                                <button type="button" @click.prevent="deletePost(post.id)"
                                                    class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Delete</button>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>