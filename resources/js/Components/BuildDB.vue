<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import ReviewRecipe from './ReviewRecipe.vue';
import RecipeResearchService from '@/services/RecipeResearchService';
//WYSBD, wire up test recipe response and fix DB write issue(s)
export default {
  props: {
    submittedData: Object,
  },
  data() {
    return {
      requestedRecipe: '',
      returnedRecipe: null,
      processingRecipeRequest: false,
      processingSQL: false,
      storedSQL: false,
    };
  },
  components: {
    ReviewRecipe,
  },
  // mounted() {
  //   // Use async/await in inherently synchronous mounted()
  //   (async () => {
  //     try {
  //       const response = await fetch('/api/testRecipeJson');
  //       const data = await response.json();
  //       const dataArray = [{ output: data.output }, { firestoreCollectionId: data.firestoreCollectionId }]
  //       this.returnedRecipe = dataArray;
  //       if (!response.ok) {
  //         console.error('HTTP error', response.status, response.statusText);
  //         return;
  //       }
  //     } catch (error) {
  //       console.error(error);
  //     }
  //   })();
  // },
  computed: {
    formattedSubmittedData() {
      return this.submittedData
        ? JSON.stringify(this.submittedData, null, 2)
        : 'No data submitted yet.';
    },
    spinning() {
      return this.processingRecipeRequest && this.requestedRecipe.length > 0;
    },
  },
  methods: {
    async getRecipe() {
      const getWebhookUrl = async () => {
        const response = await fetch('/api/webhook-url');
        if (!response.ok) {
          console.error('Failed to fetch webhook URL in /api/webhook-url');
          throw new Error('Failed to fetch webhook URL');
        }
        const data = await response.json();
        console.log(`We got the url: ${data.webhook_url}`)
        return data.webhook_url;
      };


      try {
        console.log('getting recipe');
        this.processingRecipeRequest = true;
        const webhookUrl = await getWebhookUrl();
        // const fetchUrl = `${webhookUrl}?recipe=${this.requestedRecipe.replaceAll(' ', '+').trim()}`;
        const fetchUrl = `${webhookUrl}`;
        const bodyContent = this.requestedRecipe.replaceAll(' ', '+').trim();

        const recipe = await RecipeResearchService.fetchRecipe(fetchUrl, bodyContent);
        this.returnedRecipe = recipe;
        this.processingRecipeRequest = false;
      } catch (error) {
        this.processingRecipeRequest = false;
        console.error(`Failed to fetch recipe:`, error);
      }
    },
    async storeToSQL() {
      try {
        this.processingSQL = true;
        const response = await fetch('/api/import-recipe', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.returnedRecipe)
        });
        if (!response.ok) {
          console.log('Trouble writing data to DB.');
          console.error('Status:', response.status);
          console.error('StatusText:', response.statusText);

          // Try to parse the error message from the response body
          const errorData = await response.json().catch(() => null);
          if (errorData) {
            console.error('Error details:', errorData);
            console.log('Error details:', errorData);
          } else {
            console.error('Could not parse error response.');
            console.log('Could not parse error response.');
          }

          return; // Exit early if the response is not OK
        }
      } catch (error) {
        this.processingSQL = false;
        console.error(`Failed to store to SQL:`, error);
      }
    }
  }
}
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

button {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-left: 8px;
  /* Space between spinner and text */
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
<template>

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Research, edit and save recipes</h1>
    <form @submit.prevent="getRecipe">
      <div class="mb-4">
        <label for="requestedRecipe" class="block text-lg font-medium mb-2">Desired Recipe/Food Item</label>
        <input type="text" id="requestedRecipe" v-model="requestedRecipe"
          class="w-full p-3 border rounded-md bg-gray-50" rows="10" placeholder='Menudo soup' />
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
        Submit
        <span v-show="spinning" class="spinner"></span>
      </button>
    </form>
    <!-- //WYSBD, add toaster message -->
    <!-- Display submitted data -->
    <div v-show="returnedRecipe" class="mt-6">
      <h2 class="text-xl font-semibold mb-2">Your recipe has been successfully researched and submitted</h2>
      <button @click.prevent="storeToSQL(returnedRecipe)"
        class="px-4 mb-sm py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
        Looks Good
        <span v-show="processingSQL" class="spinner"></span>
      </button>
      <ReviewRecipe v-if="returnedRecipe && returnedRecipe.length" :initialRecipe="returnedRecipe" />
      <pre>
          {{ returnedRecipe }}
        </pre>
    </div>
  </div>
</template>
