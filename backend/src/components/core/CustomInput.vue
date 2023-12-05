<template>
  <div>
    <label for="sr-only">{{ label }}</label>
    <div class="flex rounded-md mt-1 shadow-sm">
      <span
        v-if="prepend"
        class="inline-flex items-center border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm px-3 rounded-l-md"
      >
        {{ prepend }}
      </span>
      <template v-if="type === 'textarea'">
        <textarea
          :name="name"
          :required="required"
          :value="props.modelValue"
          @input="emit('update:modelValue', $event.target.value)"
          :class="inputClasses"
          :placeholder="label"
        >
        </textarea>
      </template>
      <template v-else-if="type === 'file'">
        <input
          :type="type"
          :value="props.modelValue"
          :name="name"
          :required="required"
          @input="emit('update:modelValue', $event.target.files[0])"
          :class="inputClasses"
          :placeholder="label"
        />
      </template>
      <template v-else>
        <input
          :type="type"
          :name="name"
          :required="required"
          :placeholder="label"
          :class="inputClasses"
          :value="props.modelValue"
          @input="emit('update:modelValue', $event.target.value)"
          step="0.01"
        />
      </template>
      <span
        v-if="append"
        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"
      >
        {{ append }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  modelValue: [String, Number, File],
  label: String,
  type: {
    type: String,
    default: "text",
  },
  name: String,
  required: Boolean,
  prepend: {
    type: String,
    default: "",
  },
  append: {
    type: String,
    default: "",
  },
});

const inputClasses = computed(() => {
  const cls = [
    `block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm`,
  ];

  if (props.append && !props.prepend) {
    // Show this class after the input
    cls.push(`rounded-l-md`);
  } else if (props.prepend && !props.append) {
    // Show this class in front of the input
    cls.push(`rounded-r-md`);
  } else if (!props.prepend && !props.append) {
    cls.push(`rounded-md`);
  }
  return cls.join(" ");
});

const emit = defineEmits(["update:modelValue", "change"]);
</script>
