@csrf

<div class="form-group">
    <label for="item_name">Item name</label>
    <input type="text" id="item_name" name="item_name"
           value="{{ old('item_name', $auction->item_name) }}" required>
    @error('item_name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5" required>{{ old('description', $auction->description) }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="base_price">Base price</label>
    <input type="number" id="base_price" name="base_price" step="0.01" min="0.01"
           value="{{ old('base_price', $auction->base_price) }}" required>
    @error('base_price') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="closes_at">Closing date and time</label>
    <input type="datetime-local" id="closes_at" name="closes_at"
           value="{{ old('closes_at', $auction->closes_at?->format('Y-m-d\TH:i')) }}" required>
    <div class="hint">Must be in the future.</div>
    @error('closes_at') <div class="error">{{ $message }}</div> @enderror
</div>