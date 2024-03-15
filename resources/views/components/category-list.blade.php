@props(['categoryList'])

<div {{ $attributes->merge(['class' => 'category-list text-white flex bg-slate-700']) }} >
    @if (!empty($categoryList))
        @foreach ($categoryList as $category)
            <div class="category-item relative">
                <a href="{{ route('byCategory', $category) }}" class="py-3 px-6 cursor-pointer block hover:bg-black/10">
                    {{ $category->name }}
                </a>
                <x-category-list :category-list="$category->children" class="flex-col z-50 top-[100%] hidden left-0 absolute" />
            </div>
        @endforeach
    @endif
</div>
