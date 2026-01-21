<script>
import { h, defineComponent } from 'vue';

export default defineComponent({
  name: "BaseLevel",
  props: {
    mobile: Boolean,
    type: {
      type: String,
      default: "justify-between",
    },
  },
  render() {
    const parentClass = [this.type, "items-center"];
    const parentMobileClass = ["flex"];
    const parentBaseClass = ["block", "md:flex"];
    const childBaseClass = ["flex", "items-center", "justify-center"];

    // Verificar si el slot predeterminado es una función
    const defaultSlot = this.$slots.default ? this.$slots.default() : [];

    return h(
      "div",
      {
        class: parentClass.concat(
          this.mobile ? parentMobileClass : parentBaseClass
        ),
      },
      defaultSlot.map((element, index) => {
        const childClass =
          !this.mobile && defaultSlot.length > index + 1
            ? childBaseClass.concat(["mb-6", "md:mb-0"])
            : childBaseClass;

        return h("div", { class: childClass }, [element]);
      })
    );
  },
});
</script>
