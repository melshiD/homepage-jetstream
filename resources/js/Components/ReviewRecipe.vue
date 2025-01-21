<template>
  <div class="p-6 max-w-4xl mx-auto bg-white shadow-md rounded-md">
    <h1 class="text-2xl font-bold mb-6">Edit Recipe</h1>
    <form @submit.prevent="submitForm">
      <!-- Recipe Title -->
      <div class="mb-6">
        <label for="title" class="block text-lg font-medium text-gray-700 mb-2">Recipe Title</label>
        <input id="title" type="text" v-model="recipe.title"
          class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <!-- Recipe Description -->
      <div class="mb-6">
        <label for="description" class="block text-lg font-medium text-gray-700 mb-2">Recipe Description</label>
        <input id="description" type="text" v-model="recipe.description"
          class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <!-- Ingredients Table -->
      <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-700 mb-4">Ingredients</h2>
        <table class="w-full table-auto border-collapse">
          <thead>
            <tr class="bg-gray-200 text-left">
              <th class="p-3">Ingredient</th>
              <th class="p-3">Quantity</th>
              <!-- <th class="p-3">Unit</th> -->
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ingredient, index) in recipe.ingredients" :key="index" class="border-t">
              <td class="p-3">
                <input type="text" v-model="ingredient.name"
                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
              </td>
              <td class="p-3">
                <input type="text" v-model="ingredient.quantity"
                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
              </td>
              <!-- <td class="p-3">
                <input type="text" v-model="ingredient.unit"
                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
              </td> -->
              <td class="p-3 text-center">
                <button type="button" @click="removeIngredient(index)" class="text-red-500 hover:underline">
                  Remove
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" @click="addIngredient" class="mt-4 text-blue-500 hover:underline">
          Add Ingredient
        </button>
      </div>

      <!-- Steps.prep -->
      <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-700 mb-4">Preparation Steps</h2>
        <div v-for="(step, index) in recipe.steps.preparation" :key="index" class="mb-4">
          <label :for="'step-' + index" class="block text-gray-700 font-medium mb-2">Step {{ index + 1 }}</label>
          <textarea :id="'step-' + index" v-model="recipe.steps.preparation[index]"
            class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
          <button type="button" @click="removeStep(index)" class="text-red-500 hover:underline mt-2">
            Remove Step
          </button>
        </div>
        <button type="button" @click="addStep" class="text-blue-500 hover:underline">
          Add Step
        </button>
      </div>

      <!-- Steps.cook -->
      <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-700 mb-4">Execution Steps</h2>
        <div v-for="(step, index) in recipe.steps.execution" :key="index" class="mb-4">
          <label :for="'step-' + index" class="block text-gray-700 font-medium mb-2">Step {{ index + 1 }}</label>
          <textarea :id="'step-' + index" v-model="recipe.steps.execution[index]"
            class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
          <button type="button" @click="removeStep(index)" class="text-red-500 hover:underline mt-2">
            Remove Step
          </button>
        </div>
        <button type="button" @click="addStep" class="text-blue-500 hover:underline">
          Add Step
        </button>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end space-x-4">
        <button type="button" @click="cancelEdit"
          class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
          Cancel
        </button>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    initialRecipe: {
      type: Array,
      required: true,
    },
  },
  watch:{
    initialRecipe(newRecipe){
      this.recipe = { ...newRecipe[0].output};
    },
  },
  data() {
    return {
      recipe: { ...this.initialRecipe[0].output},
    };
  },
  mounted(){
    console.log('Received initialRecipe:', this.initialRecipe);
  },
  methods: {
    addIngredient() {
      this.recipe.ingredients.push({ quantity: 0, unit: "", name: "" });
    },
    removeIngredient(index) {
      this.recipe.ingredients.splice(index, 1);
    },
    addStep() {
      this.recipe.steps.push("");
    },
    removeStep(index) {
      this.recipe.steps.splice(index, 1);
    },
    cancelEdit() {
      this.recipe = { ...this.initialRecipe };
    },
    submitForm() {
      this.$inertia.post("/recipes/update", this.recipe);
    },
  },
};
</script>

<style scoped>
table th,
table td {
  border: 1px solid #e5e7eb;
  /* Tailwind gray-200 */
}
</style>