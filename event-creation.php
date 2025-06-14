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
                            <label for="eventTitle" class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                            <input type="text" id="eventTitle" name="eventTitle"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                   bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" id="location" name="location"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                   bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                        </div>
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Submitted On</label>
                                    <?php
                                    // TODO: Replace with user's timezone from profile settings
                                    $timezone = 'America/Denver'; // Default to Denver timezone until user profile is implemented
                                    $date = new DateTime('now', new DateTimeZone($timezone));
                                    ?>
                                    <div class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300" 
                                         data-timezone="<?= htmlspecialchars($timezone) ?>"
                                         data-timestamp="<?= time() ?>">
                                        <?= $date->format('F j, Y, g:i a') ?>
                                        <span class="text-xs text-gray-500">(<?= $timezone ?>)</span>
                                    </div>
                                </div>
                                <div>
                                    <label for="eventDate" class="block text-sm font-medium text-gray-700 mb-1">Event Date</label>
                                    <input type="datetime-local" id="eventDate" name="eventDate"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                           bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contactName" class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                                    <input type="text" id="contactName" name="contactName"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                           bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                           placeholder="John Doe">
                                </div>
                                <div>
                                    <label for="eventType" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                                    <select id="eventType" name="eventType"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                            bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                                        <option value="" class="dark:bg-gray-700 dark:text-gray-100">Select event type</option>
                                        <option value="sports" class="dark:bg-gray-700 dark:text-gray-100">Sports</option>
                                        <option value="music" class="dark:bg-gray-700 dark:text-gray-100">Music</option>
                                        <option value="business" class="dark:bg-gray-700 dark:text-gray-100">Business</option>
                                        <option value="community" class="dark:bg-gray-700 dark:text-gray-100">Community</option>
                                        <option value="other" class="dark:bg-gray-700 dark:text-gray-100">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contactEmail" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                                    <input type="email" id="contactEmail" name="contactEmail"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                           bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                           placeholder="contact@example.com"
                                           pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                                           oninvalid="this.setCustomValidity('Please enter a valid email address')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div>
                                    <label for="contactPhone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                                    <input type="tel" id="contactPhone" name="contactPhone"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                           bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                           placeholder="(555) 123-4567"
                                           pattern="\(\d{3}\) \d{3}-\d{4}"
                                           oninvalid="this.setCustomValidity('Please use format: (123) 456-7890')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Event Description</h2>
                    <div>
                        <label for="eventDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="eventDescription" name="eventDescription" rows="4"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary
                                 bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"></textarea>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Event Media</h2>
                    <div class="space-y-4">
                        <!-- Featured Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Poster *</label>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Facebook -->
                        <div class="space-y-1">
                            <label for="facebookEvent" class="block text-sm font-medium text-gray-700">Facebook Event URL</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    <i class="fab fa-facebook text-blue-600"></i>
                                </span>
                                <input type="url" id="facebookEvent" name="facebookEvent"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                       placeholder="https://facebook.com/events/...">
                            </div>
                        </div>
                        
                        <!-- Facebook Business -->
                        <div class="space-y-1">
                            <label for="facebookBusiness" class="block text-sm font-medium text-gray-700">Facebook Business Page</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    <i class="fab fa-facebook-f text-blue-600"></i>
                                </span>
                                <input type="url" id="facebookBusiness" name="facebookBusiness"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                       placeholder="https://facebook.com/yourbusiness">
                            </div>
                        </div>
                        
                        <!-- Instagram -->
                        <div class="space-y-1">
                            <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    <i class="fab fa-instagram text-pink-600"></i>
                                </span>
                                <input type="text" id="instagram" name="instagram"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                       placeholder="@yourusername">
                            </div>
                        </div>
                        
                        <!-- TikTok -->
                        <div class="space-y-1">
                            <label for="tiktok" class="block text-sm font-medium text-gray-700">TikTok</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    <i class="fab fa-tiktok"></i>
                                </span>
                                <input type="text" id="tiktok" name="tiktok"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                       placeholder="@yourusername">
                            </div>
                        </div>
                        
                        <!-- Twitter -->
                        <div class="space-y-1">
                            <label for="twitter" class="block text-sm font-medium text-gray-700">Twitter</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    <i class="fab fa-twitter text-blue-400"></i>
                                </span>
                                <input type="text" id="twitter" name="twitter"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                       placeholder="@username">
                            </div>
                        </div>
                        
                        <!-- LinkedIn -->
                        <div class="space-y-1">
                            <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    <i class="fab fa-linkedin text-blue-700"></i>
                                </span>
                                <input type="url" id="linkedin" name="linkedin"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                       placeholder="https://linkedin.com/company/yourcompany">
                            </div>
                        </div>
                        
                        <!-- Twitter Hashtag -->
                        <div class="space-y-1">
                            <label for="twitterHashtag" class="block text-sm font-medium text-gray-700">Event Hashtag</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 h-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                    #
                                </span>
                                <input type="text" id="twitterHashtag" name="twitterHashtag"
                                       class="flex-1 min-w-0 block w-full h-10 px-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary
                                       bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
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
