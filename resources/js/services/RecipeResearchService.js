// resources/js/services/RecipeService.js

export default {
  async fetchRecipe(requestApiCall, bodyContent) {
    try {
      const response = await fetch(requestApiCall, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          recipe: bodyContent
        })
      });

      if (!response.ok) {
        throw new Error(`Error fetching recipe: ${response.statusText}`);
      }

      const recipe = await response.json();
      return recipe;
    } catch (error) {
      console.error(error);
      throw error;
    }
  },
};
