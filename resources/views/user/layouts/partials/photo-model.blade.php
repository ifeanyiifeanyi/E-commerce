{{-- resources/views/user/partials/photo-modal.blade.php --}}
<!-- Photo Upload Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Update Profile Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_photo_url }}" alt="Current Photo" class="current-photo mb-3" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                        <p class="text-muted">Current photo</p>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Choose new photo</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror"
                               id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                        <div class="form-text">
                            Accepted formats: JPEG, PNG, JPG, GIF. Maximum size: 2MB
                        </div>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="preview-container text-center" style="display: none;">
                        <p class="text-muted">Preview:</p>
                        <img id="photoPreview" class="preview-photo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                    </div>

                    <!-- Hidden fields to maintain other profile data -->
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="username" value="{{ $user->username }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="phone" value="{{ $user->phone }}">
                    <input type="hidden" name="address" value="{{ $user->address }}">
                    <input type="hidden" name="city" value="{{ $user->city }}">
                    <input type="hidden" name="state" value="{{ $user->state }}">
                    <input type="hidden" name="postal_code" value="{{ $user->postal_code }}">
                    <input type="hidden" name="country" value="{{ $user->country }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Update Photo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewPhoto(input) {
    const previewContainer = document.querySelector('.preview-container');
    const preview = document.getElementById('photoPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
