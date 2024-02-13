<script>
import { defineComponent, h } from "vue";

import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

export default defineComponent({
  name: "LineChart",
  components: {
    Line,
  },
  props: {
    chartId: {
      type: String,
      default: "line-chart",
    },
    width: {
      type: Number,
      default: 400,
    },
    height: {
      type: Number,
      default: 400,
    },
    cssClasses: {
      default: "",
      type: String,
    },
    styles: {
      type: Object,
      default: () => {},
    },
    plugins: {
      type: Array,
      default: () => [],
    },
    data: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const chartOptions = {
      responsive: true,
      maintainAspectRatio: false,
    };

    return () =>
      h(Line, {
        data: props.data,
        chartOptions,
        chartId: props.chartId,
        width: props.width,
        height: props.height,
        cssClasses: props.cssClasses,
        styles: props.styles,
        plugins: props.plugins,
      });
  },
});
</script>

<style scoped></style>
