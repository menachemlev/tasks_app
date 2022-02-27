<template>
  <div id="home">
    <h1 :class="fade_in_class">Tasks App</h1>
    <h2 :class="fade_in_class" v-if="popup_closed">
      The easy way to keep track of your tasks
    </h2>
    <button
      :class="fade_in_class"
      class="open-popup-btn"
      v-if="popup_closed"
      @click="openPopup"
    >
      Sign in/up
    </button>
    <img
      src="./../assests/notebook.png"
      class="note"
      v-if="popup_closed"
      @click="openPopup"
    />
    <div class="darken" @click="closePopup" v-if="!popup_closed"></div>
    <div class="popup" v-if="!popup_closed">
      <form v-if="signup_mode" @submit.prevent="submitFormSignup">
        <div class="form_title">Sign up:</div>
        <label for="name">Your full name:</label>
        <input
          type="text"
          id="name"
          placeholder="Your full name here"
          v-model="name"
        />
        <label for="email">Your email:</label>
        <input
          type="email"
          id="email"
          placeholder="Your email here"
          v-model="email"
        />
        <label for="password">Your password:</label>
        <input type="password" id="password" v-model="password" />
        <label for="password_confirm">Your password confirm:</label>
        <input
          type="password"
          id="password_confirm"
          v-model="password_confirm"
        />
        <p
          v-if="error || error_server"
          class="error"
          v-html="error || error_server"
        ></p>
        <div>
          <button type="button" @click="toggleMode">
            ◀ {{ signup_mode ? "Sign in" : "Sign up" }}
          </button>
          <button>Submit</button>
        </div>
      </form>
      <form v-if="!signup_mode" @submit.prevent="submitFormSignin">
        <div class="form_title">Sign in:</div>
        <label for="email">Your email:</label>
        <input
          type="email"
          id="email"
          placeholder="Your email here"
          v-model="email"
        />
        <label for="password">Your password:</label>
        <input type="password" id="password" v-model="password" />
        <p
          v-if="error || error_server"
          class="error"
          v-html="error || error_server"
        ></p>
        <div>
          <button type="button" @click="toggleMode">
            ◀ {{ signup_mode ? "Sign in" : "Sign up" }}
          </button>
          <button>Submit</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      password_confirm: "",
      signup_mode: true,
      form_submitted: false,
      popup_closed: true,
      fade_in_class: "fade-in",
      error_server: "",
    };
  },
  watch: {
    name() {
      this.form_submitted = false;
    },
    email() {
      this.form_submitted = false;
    },
    password() {
      this.form_submitted = false;
    },
    password_confirm() {
      this.form_submitted = false;
    },
    signup_mode() {
      this.form_submitted = false;
    },
    popup_closed(value) {
      if (!value) this.fade_in_class = "";
    },
  },
  computed: {
    passwords_same() {
      return this.password == this.password_confirm;
    },
    password_ok() {
      return /[\w|\d]{8}[\w|\d]*/g.test(this.password);
    },
    name_ok() {
      return /[\w|\s]{2}[\w|\s]*/g.test(this.name);
    },
    email_ok() {
      return /\S+@\S+\.\S+/g.test(this.email);
    },
    error() {
      let new_err = "";
      if (this.email && !this.email_ok) new_err += "Enter a valid email!<br/>";
      if (this.password && !this.password_ok)
        new_err += "Enter a valid password(8 letters of numbers)!<br/>";
      if (this.signup_mode) {
        if (this.name && !this.name_ok)
          new_err += "Enter a valid name(at-least 2 letters)!<br/>";
        if (this.password_confirm && !this.passwords_same)
          new_err += "Passwords are not the same!<br/>";
      }

      if (this.form_submitted) {
        const signup_form_missing_fields =
          this.signup_mode &&
          (!this.name ||
            !this.email ||
            !this.password ||
            !this.password_confirm);
        const signin_form_missing_fields =
          !this.signup_mode && (!this.email || !this.password);
        if (signin_form_missing_fields || signup_form_missing_fields) {
          new_err += "Missing fields!";
        }
      }
      return new_err;
    },
  },
  methods: {
    toggleMode() {
      this.error_server = "";
      this.signup_mode = !this.signup_mode;
    },
    async submitFormSignup() {
      this.error_server = "";
      this.form_submitted = true;
      if (this.error) return;
      try {
        const result = await fetch(`http://127.0.0.1:8000/api/signup`, {
          method: "POST",
          body: JSON.stringify({
            email: this.email,
            password: this.password,
            name: this.name,
          }),
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        });
        if (!result || !result.ok)
          throw new Error("Something went very wrong!");
        const res = await result.json();
        if (res.error) throw new Error(res.message);
        this.error_server = "Signing you in...";
        const { email, password, name } = this;
        this.$emit("login", { email, password, name });
        setTimeout(() => {
          this.$emit("set-page", "UserPage");
        }, 1000);
      } catch (err) {
        this.error_server = err.message;
      }
    },
    async submitFormSignin() {
      this.error_server = "";
      this.form_submitted = true;
      if (this.error) return;
      if (this.error) return;
      try {
        const result = await fetch(`http://127.0.0.1:8000/api/signin`, {
          method: "POST",
          body: JSON.stringify({
            email: this.email,
            password: this.password,
          }),
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        });
        if (!result || !result.ok)
          throw new Error("Something went very wrong!");
        const res = await result.json();
        if (!res || res.length == 0 || res.error) throw new Error(res.message);
        const { name } = res[0];
        this.$emit("login", {
          email: this.email,
          password: this.password,
          name,
        });
        this.error_server = "Signing you in...";
        setTimeout(() => {
          this.$emit("set-page", "UserPage");
        }, 1000);
      } catch (err) {
        this.error_server = err.message;
      }
    },
    openPopup() {
      this.popup_closed = false;
    },
    closePopup() {
      this.popup_closed = true;
    },
  },
};
</script>
<style>
#home {
  display: grid;
  place-items: center;
}
.darken {
  z-index: 1;
  background-color: rgba(0, 0, 0, 0.7);
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
}
.popup {
  animation: fadeIn 1s;
  z-index: 2;
}
h1 {
  text-align: center;
  color: white;
  z-index: 2;
  margin-bottom: 1em;
  font-size: 3em;
}
h2 {
  color: white;
  margin-bottom: 1em;
  z-index: 2;
}
.open-popup-btn {
  font-size: 2em;
}
form {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 1em 3em;
  min-width: 25vw;
  border: #333 solid 3px;
  border-radius: 1em;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 0 2em rgba(3, 3, 3, 0.3);
}
form::before {
  top: 0%;
  left: 50%;
  transform: translate(0, -70%);
  content: "🔑";
  z-index: 3;
  font-size: 3em;
}
form > * {
  margin: 3px;
  font-size: 1em;
}
.form_title {
  font-size: 2em;
  margin: 1em 0;
  color: #333;
  text-decoration: underline;
}
form label {
  margin-top: 1em;
  letter-spacing: 2px;
  font-style: oblique;
  font-weight: bolder;
}
form input {
  display: block;
  padding: 0.5em 1em;
  font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
  font-weight: bolder;
  border-radius: 3px;
  background-color: #333;
  color: white;
  width: 77%;
  border: 0;
}

form button {
  align-self: flex-end;
  margin: 2em 1em;
}
button {
  padding: 0.5em 1em;
  background-color: #ff0077;
  color: white;
  margin-top: 1em;
  font-size: 1.3em;
  font-family: "Gill Sans", "Gill Sans MT", Calibri, "Trebuchet MS", sans-serif;
  font-weight: bolder;
  cursor: pointer;
  transition: 0.3s;
  border: 0;
}
button:hover {
  background-color: #c51869;
}
.note {
  cursor: pointer;
  margin-top: 4.5rem;
  height: 8em;
}
</style>
