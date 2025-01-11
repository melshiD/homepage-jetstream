<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

export default {
  props: {
    submittedData: Object,
  },
  data() {
    return {
      jsonData: '', // Bind to the textarea
    };
  },
  computed: {
    formattedSubmittedData() {
      return this.submittedData
        ? JSON.stringify(this.submittedData, null, 2)
        : 'No data submitted yet.';
    },
  },
  methods: {
    submitForm() {
      const form = useForm({
        jsonData: this.jsonData,
      });

      // Post data to the backend
      form.post(route('submit-recipe-json'));
    },
  },
};
</script>

<style scoped>
textarea {
  font-family: monospace;
}

pre {
  font-family: monospace;
  white-space: pre-wrap;
  word-wrap: break-word;
}
</style>
<template>

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Submit JSON</h1>
    <form @submit.prevent="submitForm">
      <div class="mb-4">
        <label for="jsonData" class="block text-lg font-medium mb-2">JSON Data</label>
        <textarea id="jsonData" v-model="jsonData" class="w-full p-3 border rounded-md bg-gray-50" rows="10"
          placeholder='{"key": "value"}'></textarea>
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
        Submit
      </button>
    </form>

    <!-- Display submitted data -->
    <div v-if="submittedData" class="mt-6">
      <h2 class="text-xl font-semibold mb-2">Submitted Data</h2>
      <pre class="bg-gray-100 p-4 rounded-md overflow-auto">
        {{ formattedSubmittedData }}
      </pre>
    </div>
  </div>
</template>
