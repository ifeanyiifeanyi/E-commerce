 <!-- Edit Category Modal -->
 <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
 aria-hidden="true">
 <div class="modal-dialog">
     <div class="modal-content">
         <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
             @csrf
             @method('PUT')
             <div class="modal-header">
                 <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="mb-3">
                     <label for="edit-name" class="form-label">Name</label>
                     <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit-name"
                         name="name" required>
                     @error('name')
                         <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                 </div>
                 <div class="mb-3">
                     <label for="edit-description" class="form-label">Description</label>
                     <textarea class="form-control @error('description') is-invalid @enderror" id="edit-description" name="description"
                         rows="3"></textarea>
                     @error('description')
                         <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                 </div>
                 <div class="mb-3">
                     <label for="edit-image" class="form-label">Image</label>
                     <input type="file" class="form-control @error('image') is-invalid @enderror"
                         id="edit-image" name="image" accept="image/*">
                     @error('image')
                         <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                     <small class="text-muted">Max size: 2MB. Allowed types: JPEG, PNG, JPG, GIF</small>
                     <div id="current-image" class="mt-2">
                         <img src="" alt="" class="img-thumbnail" style="max-height: 100px;">
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary">Update Category</button>
             </div>
         </form>
     </div>
 </div>
</div>
