<template>
  <div class="flex flex-wrap gap-1">
    <Sortable
      :list="imgUrls"
      item-key="id"
      @end="onImageDragEnd"
      class="flex gap-1 flex-wrap"
    >
      <template #item="{ element: image, index }">
        <div
          class="w-[120px] h-[120px] relative rounded border border-dashed items-center justify-center flex overflow-hidden hover:border-purple-500"
        >
          <img
            :src="image.url"
            class="max-w-full max-h-full"
            :class="image.deleted ? 'opacity-50' : ''"
          />
          <small
            v-if="image.deleted"
            class="absolute bottom-0 right-0 w-100 left-0 px-2 py-1 text-white flex justify-between items-center bg-black"
          >
            To be deleted
            <svg
              @click="revertImage(image)"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-4 h-4 cursor-pointer"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"
              />
            </svg>
          </small>
          <span class="absolute right-1 top-1 cursor-pointer" @click="removeImage(image)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18 18 6M6 6l12 12"
              />
            </svg>
          </span>
        </div>
      </template>
    </Sortable>

    <div
      class="w-[120px] h-[120px] relative rounded border border-dashed items-center justify-center flex overflow-hidden hover:border-purple-500"
    >
      <span> Upload </span>
      <div>
        <input
          type="file"
          class="absolute left-0 bottom-0 w-full h-full right-0 opacity-0"
          @change="onFileChange"
          multiple
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { Sortable } from "sortablejs-vue3";
import { v4 as uuidv4 } from "uuid";

const files = ref([]);
const imgUrls = ref([]);
const deletedImages = ref([]);
const imagePositions = ref([]);

const props = defineProps(["modelValue", "deletedImages", "images"]);
const emit = defineEmits([
  "update:modelValue",
  "update:deletedImages",
  "update:imagePositions",
]);

function onFileChange($event) {
  const chosenFiles = [...$event.target.files];
  files.value = [...files.value, ...chosenFiles];
  $event.target.value = "";
  const allPromises = [];
  for (let file of chosenFiles) {
    file.id = uuidv4();
    const promise = readFile(file);
    allPromises.push(promise);
    promise.then((url) => {
      imgUrls.value.push({
        url,
        id: file.id,
      });
    });
  }

  Promise.all(allPromises).then(() => {
    updateImagePositions();
  });
  emit("update:modelValue", files.value);
}

function readFile(file) {
  return new Promise((resolve, reject) => {
    const fileReader = new FileReader();
    fileReader.readAsDataURL(file);
    fileReader.onload = () => {
      resolve(fileReader.result);
    };
    fileReader.onerror = reject;
  });
}

function removeImage(image) {
  if (image.isProp) {
    deletedImages.value.push(image.id);
    image.deleted = true;

    emit("update:deletedImages", deletedImages.value);
  } else {
    files.value = files.value.filter((f) => f.id !== image.id);
    imgUrls.value = imgUrls.value.filter((f) => f.id !== image.id);

    emit("update:modelValue", files.value);
  }

  updateImagePositions();
}

function revertImage(image) {
  if (image.isProp) {
    deletedImages.value = deletedImages.value.filter((id) => id !== image.id);
    image.deleted = false;

    emit("update:deletedImages", deletedImages.value);
  }
}

function onImageDragEnd(ev) {
  const { oldIndex, newIndex } = ev;
  const [tmp] = imgUrls.value.splice(oldIndex, 1);
  imgUrls.value.splice(newIndex, 0, tmp);

  updateImagePositions();
}

function updateImagePositions() {
  imagePositions.value = Object.fromEntries(
    imgUrls.value.filter((im) => !im.deleted).map((im, ind) => [im.id, ind + 1])
  );

  emit("update:imagePositions", imagePositions.value);
}

watch(
  "props.images",
  () => {
    imgUrls.value = [
      ...imgUrls.value,
      ...props.images.map((im) => ({
        ...im,
        isProp: true,
      })),
    ];

    updateImagePositions();
  },
  { immediate: true, deep: true }
);

onMounted(() => {
  emit("update:modelValue", []);
  emit("update:deletedImages", deletedImages.value);
});
</script>
