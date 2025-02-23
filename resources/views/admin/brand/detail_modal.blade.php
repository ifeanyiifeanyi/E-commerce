<div class="modal fade" id="brandDetailsModal" tabindex="-1" aria-labelledby="brandDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandDetailsModalLabel">Brand Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="text-center col-md-4">
                        <img id="brandLogo" src="" alt="Brand Logo" class="mb-3 img-fluid">
                    </div>
                    <div class="col-md-8">
                        <table class="table">
                            <tr>
                                <th>Name:</th>
                                <td id="brandName"></td>
                            </tr>
                            <tr>
                                <th>Website:</th>
                                <td id="brandWebsite"></td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td id="brandDescription"></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td id="brandStatus"></td>
                            </tr>
                            <tr>
                                <th>Featured:</th>
                                <td id="brandFeatured"></td>
                            </tr>
                        </table>
                        <div class="mt-3">
                            <h6>SEO Information</h6>
                            <table class="table">
                                <tr>
                                    <th>Meta Title:</th>
                                    <td id="brandMetaTitle"></td>
                                </tr>
                                <tr>
                                    <th>Meta Description:</th>
                                    <td id="brandMetaDescription"></td>
                                </tr>
                                <tr>
                                    <th>Meta Keywords:</th>
                                    <td id="brandMetaKeywords"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>