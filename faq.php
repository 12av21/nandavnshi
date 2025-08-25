<?php
$page_title = "Frequently Asked Questions";
$page_content = "
    <div class='faq-section'>
        <div class='row mb-5'>
            <div class='col-lg-8 mx-auto text-center'>
                <h2>Frequently Asked Questions</h2>
                <p class='lead'>Find answers to common questions about NSCT</p>
                <div class='divider bg-primary mx-auto' style='width: 80px; height: 3px;'></div>
            </div>
        </div>
        
        <div class='row justify-content-center'>
            <div class='col-lg-8'>
                <div class='accordion' id='faqAccordion'>
                    <!-- FAQ Item 1 -->
                    <div class='accordion-item mb-3 border-0 shadow-sm'>
                        <h2 class='accordion-header' id='headingOne'>
                            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapseOne' aria-expanded='true' aria-controls='collapseOne'>
                                How do I become a member of NSCT?
                            </button>
                        </h2>
                        <div id='collapseOne' class='accordion-collapse collapse' aria-labelledby='headingOne' data-bs-parent='#faqAccordion'>
                            <div class='accordion-body'>
                                Fill out the membership form on our website or visit our office with ID proof, address proof, photographs, and membership fee. Processing takes 7-10 working days.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class='accordion-item mb-3 border-0 shadow-sm'>
                        <h2 class='accordion-header' id='headingTwo'>
                            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapseTwo' aria-expanded='false' aria-controls='collapseTwo'>
                                What are the membership benefits?
                            </button>
                        </h2>
                        <div id='collapseTwo' class='accordion-collapse collapse' aria-labelledby='headingTwo' data-bs-parent='#faqAccordion'>
                            <div class='accordion-body'>
                                Benefits include financial aid, scholarships, event access, career support, and legal assistance.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class='accordion-item mb-3 border-0 shadow-sm'>
                        <h2 class='accordion-header' id='headingThree'>
                            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapseThree' aria-expanded='false' aria-controls='collapseThree'>
                                How to apply for financial aid?
                            </button>
                        </h2>
                        <div id='collapseThree' class='accordion-collapse collapse' aria-labelledby='headingThree' data-bs-parent='#faqAccordion'>
                            <div class='accordion-body'>
                                Download the form from our website, complete it with required documents, and submit to our office or email financialaid@nsct.org. Processing takes 10-14 days.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class='text-center mt-5'>
                    <h4>Still have questions?</h4>
                    <p>Contact our support team for assistance</p>
                    <a href='contact.php' class='btn btn-primary px-4'>
                        <i class='fas fa-envelope me-2'></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>";
include 'templates/page-template.php';
?>
