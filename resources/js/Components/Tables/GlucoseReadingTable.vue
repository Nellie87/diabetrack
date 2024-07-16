<template>
  <div>
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Date Time
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Glucose Level
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Notes
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-white-200">
        <tr v-for="(reading, index) in glucoseReadings" :key="index">
          <td class="px-6 py-4 whitespace-nowrap">
            {{ reading.Datetime }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            {{ reading.GlucoseLevel }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            {{ reading.Notes }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    glucoseReadings: {
      type: Array,
      required: true
    }
  },
  mounted() {
    console.log('Glucose readings prop:', this.glucoseReadings); // Log received prop
  }
}
</script>

<style>
/* Optional: Add styling using Tailwind CSS or your preferred CSS framework */
</style>
