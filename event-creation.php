<?php 
include __DIR__ . '/includes/dashboard-header.php';
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Event</h1>
        
        <!-- Event Creation Form -->
        <form id="eventForm" class="bg-white shadow rounded-lg p-6 mb-8" enctype="multipart/form-data">
            <div class="space-y-6">
                <!-- Basic Information Section -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="eventTitle" class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                            <input type="text" id="eventTitle" name="eventTitle" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label for="eventDate" class="block text-sm font-medium text-gray-700 mb-1">Event Date *</label>
                            <input type="datetime-local" id="eventDate" name="eventDate" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                            <input type="text" id="location" name="location" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label for="eventType" class="block text-sm font-medium text-gray-700 mb-1">Event Type *</label>
                            <select id="eventType" name="eventType" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                <option value="">Select event type</option>
                                <option value="sports">Sports</option>
                                <option value="music">Music</option>
                                <option value="business">Business</option>
                                <option value="community">Community</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Event Description</h2>
                    <div>
                        <label for="eventDescription" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea id="eventDescription" name="eventDescription" rows="4" required
                                 class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Event Media</h2>
                    <div class="space-y-4">
                        <!-- Featured Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="featuredImage" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="featuredImage" name="featuredImage" type="file" accept="image/*" class="sr-only" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Media Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Media (Optional)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="additionalMedia" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload files</span>
                                            <input id="additionalMedia" name="additionalMedia[]" type="file" multiple class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF, MP4 up to 50MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Section -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Social Media</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="facebookEvent" class="block text-sm font-medium text-gray-700 mb-1">Facebook Event URL</label>
                            <input type="url" id="facebookEvent" name="facebookEvent"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                                   placeholder="https://facebook.com/events/...">
                        </div>
                        <div>
                            <label for="twitterHashtag" class="block text-sm font-medium text-gray-700 mb-1">Twitter Hashtag</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    #
                                </span>
                                <input type="text" id="twitterHashtag" name="twitterHashtag"
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary"
                                       placeholder="YourEventName">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Save Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
