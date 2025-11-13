<template>
    <div class="d-flex flex-column">
        <footer class="p-2">
            <div class="row align-items-center">
                <div class="col-3">
                    <div style="padding: 60px;">
                        <ul class="d-flex justify-content-center social-platforms gap-3">
                            <li class="social-twitter"><i class="fab fa-twitter fs-4"></i></li>
                            <li class="social-instagram"><i class="fab fa-instagram fs-4"></i></li>
                            <li class="social-youtube"><i class="fab fa-youtube fs-4"></i></li>
                            <li class="social-linkedin"><i class="fab fa-linkedin fs-4"></i></li>
                        </ul>
                    </div>
                </div>
    
                <div class="col-3">
                    <h5 class="mb-3">Use cases</h5>
                    <ul>
                        <li>UI Design</li>
                        <li>UX Design</li>
                        <li>Wireframing</li>
                        <li>Diagramming</li>
                    </ul>
                </div>
    
                <div class="col-3">
                    <h5 class="mb-3">Explore</h5>
                    <ul>
                        <li>Design</li>
                        <li>Prototyping</li>
                        <li>Development features</li>
                        <li>Design systems</li>
                    </ul>
                </div>
    
                <div class="col-3">
                    <h5 class="mb-3">Resources</h5>
                    <ul>
                        <li>Blog</li>
                        <li>Best practices</li>
                        <li>Colors</li>
                        <li>Color wheel</li>
                    </ul>
                </div>
            </div>
         </footer>

        <hr>
        <p class="text-center">Copyright &copy; {{ new Date().getFullYear() }} All rights reserved.</p>
     </div>
</template>


 <script>
 export default {
     name: 'Footer',
     data() {
         return {
            //
         };
     }
 };
 </script>
 
 <style scoped>
 ul {
    margin: 0;
    padding: 0;
    overflow: auto;
 }
 
 ul li {
    list-style-type: none;
 }

 ul li:hover{
    cursor: pointer;
 }

.social-youtube:hover {
    color: #dc4a38;
}

.social-linkedin:hover {
    color: #0077b5;
}

.social-instagram:hover {
    color: #d42dde;
}

.social-twitter:hover {
    color: #5da9dd;
}
</style>
 