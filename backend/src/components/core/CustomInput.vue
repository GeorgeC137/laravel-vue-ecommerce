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
      <template v-if="type === 'select'">
        <select
          :name="name"
          :required="required"
          :value="props.modelValue"
          @change="onChange($event.target.value)"
          :class="inputClasses"
        >
          <option v-for="option in selectOptions" :value="option.key">
            {{ option.text }}
          </option>
        </select>
      </template>
      <template v-else-if="type === 'textarea'">
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
          @input="emit('change', $event.target.files[0])"
          :class="inputClasses"
          :placeholder="label"
        />
      </template>
      <template v-else-if="type === 'checkbox'">
        <input
          :id="id"
          :checked="props.modelValue"
          @change="emit('update:modelValue', $event.target.checked)"
          :type="type"
          :name="name"
          :required="required"
          class="block h-4 w-4 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
  modelValue: [String, Number, File, Boolean],
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
  selectOptions: Array,
});

const id = computed(() => {
  if (props.id) {
    return props.id;
  }
  return `id-${Math.floor(1000000 + Math.random() * 1000000)}`;
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

function onChange(value) {
  emit("update:modelValue", value);
  emit("change", value);
}
</script>
