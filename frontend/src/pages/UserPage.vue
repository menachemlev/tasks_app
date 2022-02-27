<template>
  <div id="user">
    <div @click="logout" class="logout">◀ Logout</div>
    <h1>Welcome {{ this.user.name }}!</h1>
    <input
      placeholder="task title"
      type="text"
      v-model="task"
      @keydown.enter="addTask"
    />
    <textarea
      rows="5"
      v-model="task_desc"
      @keydown.enter="addTask"
      placeholder="task description"
    ></textarea>
    <div>
      <button @click="addTask">Add task</button>
      &nbsp;&nbsp;<button @click="backupTasks">
        💾 Backup <span v-if="loading_backup">&nbsp;⏳</span>
      </button>
    </div>
    <br /><br />
    <ul>
      <details v-for="(t, idx) in tasks" ref="task" :key="t.title">
        <summary>
          <span class="remove-task" @click.stop="removeTask(idx)">X</span>
          <span class="taskup" v-if="idx !== 0" @click.stop="taskUp(idx)"
            >⬆</span
          >
          <span
            class="taskdown"
            v-if="idx !== tasks.length - 1"
            @click.stop="taskDown(idx)"
            >⬇</span
          >

          <small>({{ idx + 1 }})</small> {{ t.title }}
        </summary>
        <p>{{ t.desc }}</p>
      </details>
    </ul>
  </div>
</template>

<script>
export default {
  props: ["user"],
  mounted() {
    const load = async () => {
      const result = await fetch(`http://127.0.0.1:8000/api/gettasks`, {
        method: "POST",
        body: JSON.stringify({
          email: this.user.email,
          password: this.user.password,
        }),
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      });
      if (!result || !result.ok) throw new Error("Something went very wrong!");
      const res = await result.json();
      if (!res) throw new Error("Could not create user!!");
      return res;
    };
    load()
      .then((res) => {
        res.forEach((t) =>
          this.tasks.push({ title: t.title, desc: t.description })
        );
      })
      .catch((err) => console.error(err));
  },
  data() {
    return {
      tasks: [],
      task: "",
      task_desc: "",
      loading_backup: false,
    };
  },
  methods: {
    logout() {
      this.$emit("logout");
    },
    addTask() {
      if (this.task != "") {
        const same_task = this.tasks.find((t) => t.title == this.task);
        if (same_task) {
          same_task.desc = this.task_desc;
        } else this.tasks.unshift({ title: this.task, desc: this.task_desc });
      }
    },
    async backupTasks() {
      this.loading_backup = true;
      try {
        const result = await fetch(`http://127.0.0.1:8000/api/tasks`, {
          method: "POST",
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
            tasks: this.tasks,
          }),
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        });
        if (!result || !result.ok)
          throw new Error("Something went very wrong!");
        const res = await result.json();
        if (!res) throw new Error("Could not save tasks!");
        if (res.error) throw new Error(res.error);
      } catch (err) {
        console.error(err);
      }
      this.loading_backup = false;
    },
    removeTask(idx) {
      this.tasks.splice(idx, 1);
    },
    taskUp(idx) {
      if (idx == 0) return;
      const above_task = {
        title: this.tasks[idx - 1].title,
        desc: this.tasks[idx - 1].desc,
      };
      this.tasks[idx - 1].title = this.tasks[idx].title;
      this.tasks[idx - 1].desc = this.tasks[idx].desc;
      this.tasks[idx].title = above_task.title;
      this.tasks[idx].desc = above_task.desc;
    },
    taskDown(idx) {
      if (idx == this.tasks.length - 1) return;
      const below_task = {
        title: this.tasks[idx + 1].title,
        desc: this.tasks[idx + 1].desc,
      };
      this.tasks[idx + 1].title = this.tasks[idx].title;
      this.tasks[idx + 1].desc = this.tasks[idx].desc;
      this.tasks[idx].title = below_task.title;
      this.tasks[idx].desc = below_task.desc;
    },
  },
};
</script>

<style scoped>
#user {
  display: grid;
  place-items: center;
}
h1 {
  animation: fadeIn 1s;
  font-size: 2.7em;
}
ul {
  list-style-type: none;
  width: 100%;
  display: grid;
  max-height: 30vh;
  place-items: center;
  overflow-y: scroll;
  overflow-x: hidden;
}
details {
  margin: 0.3em 0;
  position: relative;
  color: #333;
  width: 80%;
  max-width: 33vw;
  background-color: aliceblue;
  padding: 0.6em 3em;
  border-radius: 0.3em;
  text-align: center;
  font-weight: bolder;
  font-size: 1em;
}
summary {
  font-size: 1.5em;
  margin-bottom: 4px;
}
details p {
  margin: 2em 0;
}
.remove-task,
.taskup,
.taskdown {
  position: absolute;
  font-size: 1em;
  top: 5%;
  right: 5%;
  color: red;
  cursor: pointer;
}
.taskup {
  top: 1%;
  right: 12%;
}
.taskdown {
  top: 1%;
  right: 19%;
}
input,
textarea {
  font-family: "Courier New", Courier, monospace;
  font-size: 1.2em;
  display: block;
  padding: 0.3em;
  font-weight: bolder;
  border: 0;
  width: 33vw;
  background: #333;
  color: white;
  border: rgba(255, 255, 255, 0.3) 3px solid;
  transition: 0.3s;
}
textarea {
  margin-top: 1em;
  resize: none;
}
input:focus {
  box-shadow: 0 0 1em rgba(255, 255, 255, 0.3);
}
input:focus::placeholder,
textarea:focus::placeholder {
  color: white;
}
.logout {
  position: fixed;
  top: 5%;
  left: 5%;
  cursor: pointer;
  font-weight: bolder;
  font-size: 1.4em;
  color: white;
  transition: 0.3s;
}
.logout:hover {
  letter-spacing: 1px;
}
</style>
