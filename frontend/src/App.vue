<template>
  <component
    :user="user"
    @login="loginUser"
    @logout="logoutUser"
    @set-page="setPage"
    :is="currentPage"
  >
  </component>
</template>

<script>
import HomePage from "./pages/HomePage.vue";
import UserPage from "./pages/UserPage.vue";

export default {
  components: {
    HomePage,
    UserPage,
  },
  data() {
    return {
      currentPage: "HomePage",
      user: { name: "", email: "", password: "" },
    };
  },
  methods: {
    setPage(page) {
      this.currentPage = page;
    },
    loginUser(userObj) {
      this.user = userObj;
    },
    logoutUser() {
      this.user = {};
      this.currentPage = "HomePage";
    },
  },
};
</script>
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
html {
  font-family: "Jost", sans-serif;
}
body {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  height: 100vh;
  padding: 10vh;
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.637), rgba(0, 0, 0, 0.61)),
    url("./assests/background.jpg");
  background-size: cover;
  background-attachment: fixed;
}

.fade-in {
  animation: fadeInFromTop 1.2s backwards;
}
.error {
  color: white;
  background-color: rgb(216, 49, 49);
  border-radius: 1em;
  padding: 1em 2em;
  margin-top: 2em;
}
@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}
@keyframes fadeInFromTop {
  0% {
    opacity: 0;
    transform: translateY(-200%);
  }
  80% {
    transform: translateY(20%);
  }
  100% {
    opacity: 1;
    transform: translateX(0%);
  }
}
</style>
